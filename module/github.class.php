<?php
/**
 * Github webhook relay
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_github extends module {
	private $socket;

	public function init() {
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

		if (!socket_bind($socket, '0.0.0.0', 8081)) {
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

		$msg = sprintf("[GIT] %d commits pushed to %s [%s]"
			, count($payload->commits)
			, $payload->repository->name
			, $payload->compare
		);

		$this->parent()->send(irc::PRIVMSG("#hackers", $msg));
	}

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
			print("GIT: Not POST\n");
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
			print("GIT: Missing content length\n");
			socket_close($client);
			return FALSE;
		}

		$payload = "";
		$bytes = socket_recv($client, $payload, $headers['Content-Length'] + 1, MSG_WAITALL);
		printf("GIT: Got %d bytes, expected %d: %s\n"
			, $bytes
			, $headers['Content-Length'] + 1
			, $payload
		);

		list($key,$payload) = explode('=', $payload, 2);

		if (trim($key) != 'payload') {
			printf("GIT: Expected key to be payload, was'%s'\n", $key);
		}

		// Clean up
		socket_close($client);

		// Return payload
		return json_decode(urldecode($payload));
	}
}
