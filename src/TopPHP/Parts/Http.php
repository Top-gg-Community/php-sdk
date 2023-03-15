<?php
/**
 * This file is respectively apart of the TopPHP project.
 *
 * Copyright (c) 2021-present James Walston and Federico Cosma
 * Some rights are reserved.
 *
 * This copyright is subject to the MIT license which
 * fully entails the details in the LICENSE file.
 */

namespace TopPHP\Parts;
use TopPHP\Parts\Cache;

class Http {
    protected static function request(string $protocol, string $url, string $token, mixed $body = NULL) {
        $body = $body ?? [];
        $options = stream_context_create(array( 
          'http' => array(
              'header' => "Accept: application/json\r\n" .
                          "Authorization: {$token}\r\n" . 
                          "User-Agent: request\r\n",
              'method' => $protocol,
              'content' => http_build_query($body),
              // 'ignore_errors' => true
          )
        )); 
        $response = json_decode(file_get_contents($url, false, $options));
        return $response;
    }
  
    public static function get(string $url, string $token, mixed $body = NULL) {
        if (Cache::is($url)) {
          return Cache::get($url);
        }
        $response = self::request('GET', $url, $token, $body);
        Cache::set($url, $response);
        return $response;
    }
  
    public static function fetch(string $url, string $token, mixed $body = NULL) {
        $response = self::request('GET', $url, $token, $body);
        return $response;
    }

    public static function post(string $url, string $token, mixed $body = NULL) {
        return self::request('POST', $url, $token, $body);
    } 
}