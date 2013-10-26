<?php
/**
 * Google module
 *
 * @author Gussi <gussi@gussi.is>
 */

class module_google extends module {
    public function init() {
        $this->module('fantasy')->register(['g', 'google'], function($data) {
            // Check for arguments presence
            if (empty($data['args'])) {
                $this->log->debug('Got no args');
                return;
            }

            // Search
            $ret = $this->search($data['args']);
            if (empty($ret->responseData->results)) {
                $this->log->debug('Got no results');
                return;
            }

            // Get first result
            $result = (array)array_shift($ret->responseData->results);

            $msg = sprintf("[GOOGLE] %s - %s"
                , $result['titleNoFormatting']
                , $result['url']
            );

            $this->log->debug($msg);
            $this->parent->send(IRC::PRIVMSG($data['to'], $msg));
        });
    }

    /**
     * Search google
     *
     * @param $search           Search parameter
     */
    public function search($search) {
        $url = sprintf("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=%s&key=%s"
            , urlencode($search)
            , $this->config['api-key']
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'phpirc/Zod');
        $json = json_decode(curl_exec($ch));
        curl_close($ch);

        return $json;
    }
}
