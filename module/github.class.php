<?php
/**
 * Github webhook relay
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_github extends module {
    private $socket;

    public function init() {
        // Merge default and user config
        $this->config = array_merge([
            'ip'                    => '0.0.0.0',   // Listen on given interface
            'port'                  => '8080',      // Listen on given port
            'max_push_messages'     => 5,           // Max messages per push
            'repository'            => [
                '_default'              => [
                    'channel'               => '#spam',
                ],
            ],
        ], $this->config);

        $this->socket = $this->setup_socket();
        $this->timer(1, [$this, 'tick']);
    }

    /**
     * Setup socket for incoming connections
     */
    private function setup_socket() {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$socket) {
            throw new Exception("Unable to create socket");
        }

        if (!socket_bind($socket, $this->config['ip'], $this->config['port'])) {
            throw new Exception("Unable to bind socket");
        }

        if (!socket_listen($socket)) {
            throw new Exception("Unable to listen to incoming connections");
        }

        socket_set_nonblock($socket);

        return $socket;
    }

    /**
     * Timer tick, poll for connections
     */
    public function tick() {
        $client = socket_accept($this->socket); // Accept connection
        $payload = $this->handle_client($client);

        if (!$payload) {
            // No payload, return nothing
            return;
        }

        $config = isset($this->config['repositories'][$payload->repository->name])
            ? $this->config['repositories'][$payload->repository->name]
            : $this->config['repositories']['_default'];

        $msg = sprintf("[GIT] %s pushed %d commits to %s [%s]"
            , $payload->pusher->name
            , count($payload->commits)
            , $payload->repository->name
            , $payload->compare
        );
        $this->parent()->send(irc::PRIVMSG($config['channel'], $msg));

        $count = 0;
        foreach ($payload->commits as $commit) {
            $msg = sprintf("[GIT] %s -%s [%s]"
                , explode("\n", $commit->message)[0]
                , $commit->author->username
                , $commit->url
            );

            $this->parent()->send(irc::PRIVMSG($config['channel'], $msg));

            // Break on max push messages
            if ($count++ >= $this->config['max_push_messages']) {
                break;
            }
        }
    }

    /**
     * Handle client socket
     *
     * @param $client               Client socket
     */
    private function handle_client($client) {
        // Check for socket presence
        if (!$client) {
            return FALSE;
        }

        // Max recv bytes
        $len = 1024;

        // Expect HTTP POST header
        $data = socket_read($client, $len, PHP_NORMAL_READ);
        if ($data == 'POST / HTTP/1.1') {
            $this->log->error("Expected POST / HTTP header, got '%s'", $data);
            socket_close($client);
            return FALSE;
        }

        $headers = [];
        // Get HTTP headers
        while ($chunk = socket_read($client, $len, PHP_NORMAL_READ)) {
            // Break on CR
            if ($chunk == "\r") {
                break;
            }

            // Continue on LF
            if ($chunk == "\n") {
                continue;
            }

            // Expecting header, add it
            list($key, $val) = explode(': ', $chunk);
            $headers[$key] = trim($val);
        }

        if (!isset($headers["Content-Length"])) {
            $this->log->error("Missing content length from HTTP headers");
            socket_close($client);
            return FALSE;
        }

        $payload = "";
        $bytes = socket_recv($client, $payload, $headers['Content-Length'] + 1, MSG_WAITALL);
        $this->log->debug("Got %d bytes, expected %d: %s"
            , $bytes
            , $headers['Content-Length'] + 1
            , $payload
        );

        // Clean up
        socket_close($client);


        // Get payload
        list($key,$payload) = explode('=', $payload, 2);
        if (trim($key) != 'payload') {
            $this->log->debug("GIT: Expected key to be payload, got '%s'", $key);
        }

        // Return payload
        return json_decode(urldecode($payload));
    }
}
