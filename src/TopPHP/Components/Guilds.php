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

namespace TopPHP\Components;
use TopPHP\TopPHP;
use TopPHP\Parts\Http;
use TopPHP\Parts\Cache;
use TopPHP\Parts\Collection;
use TopPHP\Components\Guild;

class Guilds {
    protected string $token;
    protected string $endpoint;
    protected TopPHP $parent;

    // Let's create the magic!
    function __construct(TopPHP $parent) {
        $this->parent = $parent;
    }

    public function top() : Collection|NULL {
        $data = Http::pureGet('https://top.gg/api/search?q=%20&currentSpace=discord');
        if (!$data) {
            if (stripos($http_response_header[0], '401') !== false) {
                $this->parent->callableException('authenticationException', [
                    "name" => "Unhautorized",
                    "description" => "Unhautorized! Actual token ({$this->parent->token}) is not valid!",
                    "token" => $this->parent->token,
                    "headers" => $http_response_header
                ]);
            } else {
                $this->parent->callableException('commonException', [
                    "name" => "Undefined",
                    "description" => "Top.gg didn't say anything :(",
                    "headers" => $http_response_header
                ]); 
            }
            return NULL;
        } else {
            $collection = new Collection();
            foreach ($data->results->servers as $server) {
                $collection->add($server->id, new Guild($server, $this->parent));
            }
            return $collection;
        }
    }

    public function get(string $name, string|NULL $id = NULL) : Guild|NULL {
        $data = Http::pureGet("https://top.gg/api/search?q={$name}&currentSpace=discord&limit=20");
        if (!$data) {
            if (stripos($http_response_header[0], '401') !== false) {
                $this->parent->callableException('authenticationException', [
                    "name" => "Unhautorized",
                    "description" => "Unhautorized! Actual token ({$this->parent->token}) is not valid!",
                    "token" => $this->parent->token,
                    "headers" => $http_response_header
                ]);
            } else {
                $this->parent->callableException('commonException', [
                    "name" => "Undefined",
                    "description" => "Top.gg didn't say anything :(",
                    "headers" => $http_response_header
                ]); 
            }
            return NULL;
        } elseif (count($data->results->servers) == 0) {
            $this->parent->callableException('notFoundException', [
                "name" => "Not found!",
                "description" => "The requested guild probably doesn't exists (yet)",
                "type" => "complete",
                "headers" => $http_response_header
            ]);
            return NULL;
        } else {
            // Retrive informations
            if ($id === NULL) {
                return new Guild($data->results->servers[0], $this->parent);
            } else {
                // ID CHECK
                foreach ($data->results->servers as $server) {
                    if ($server->id == $id) {
                        return new Guild($server, $this->parent);
                    }
                }
                // Thrown a notFoundException
                $this->parent->callableException('notFoundException', [
                    "name" => "Not found!",
                    "description" => "The requested guild probably doesn't exists (yet)",
                    "type" => "idMatch",
                    "headers" => $http_response_header
                ]);
            }
        }
    }
}