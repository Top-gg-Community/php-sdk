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
use TopPHP\Components\User;
use TopPHP\Components\Bots;

class Bot {
    public string $id;
    public array $owners;
    public Bots $bots;
    protected string $token;
    protected string $endpoint;
    protected array $fillable = [
        'invite',
        'website',
        'support',
        'github',
        'longdesc',
        'shortdesc',
        'prefix',
        'lib',
        'clientid',
        'avatar',
        'id',
        'username',
        'date',
        'guilds',
        'shards',
        'monthlyPoints',
        'points',
        'certifiedBot',
        'owners',
        'tags',
        'vanity',
        'server_count',
        'shard_count',
        'bannerUrl',
        'donatebotguildid'
    ];
    protected array $needed = [
        'shortdesc',
        'prefix',
        'clientid',
        'id',
        'username',
        'date',
        'guilds',
        'shards',
        'monthlyPoints',
        'points',
        'certifiedBot',
        'owners',
        'tags'
    ];
    protected TopPHP $parent;

    // Initialize this function
    function __construct(object $botdata, TopPHP $parent) {
        $this->parent = $parent;
        $this->bots = new Bots($parent);
        foreach ($this->fillable as $needed) {
            if (empty($botdata->{$needed})) {
                $this->parent->callableException('dataMissingException', [
                    "name" => "Some data are missing during the bot object creation!",
                    "description" => "Some data are missing during the bot object creation!",
                    "bot_id" => $botdata->id,
                    "missing_data" => $needed
                ], $this->parent);
                continue;
            }
            $this->{$needed} = $botdata->{$needed};
        }
        if ($parent->loadAllData) {
            // Load users and make an array
            $this->owners = [];
            foreach ($botdata->owners as $owner) {
                array_push($this->owners, new User(Http::get("{$this->parent->endpoint}/users/{$owner}", $this->parent->token), $this->parent));
            }
        }
    }

    // Get stats
    public function stats() : object {
        $data = Http::get("{$this->parent->endpoint}/bots/{$this->id}/stats", $this->parent->token);
        if (!$data) {
            $this->parent->callableException('connectionException', [
                "description" => "Something went wrong during the HTTP/1.1 request to the bot info",
                "bot_id" => $this->id,
                "headers" => $http_response_header
            ]);
            return new \stdClass;
        }
        return $data;
    }

    public function hasBeenVotedBy(string $id) : bool {
        $data = Http::get("{$this->parent->endpoint}/bots/{$this->id}/check?userId={$id}", $this->parent->token);
        if (!$data) {
            $this->parent->callableException('connectionException', [
                "description" => "Something went wrong during the HTTP/1.1 request to the bot info",
                "bot_id" => $this->id,
                "headers" => $http_response_header
            ]);
            return new \stdClass;
        }
        if ($data->voted == 1) {
            return true;
        }
        return false;
    }

    // Alias for vote checking
    public function hasBeenVoted(string $id) : bool {
        return $this->hasBeenVotedBy($id);
    }

    // Alias for vote checking
    public function votedBy(string $id) : bool {
        return $this->hasBeenVotedBy($id);
    }

    // All votes, 1000 max!
    public function votes() : array|NULL {
        $data = Http::get("{$this->parent->endpoint}/bots/{$this->id}/votes", $this->parent->token);
        if (!$data) {
            if (stripos($http_response_header[0], '401') !== false) {
                $this->parent->callableException('authenticationException', [
                    "name" => "Unhautorized",
                    "description" => "Unhautorized! Actual token ({$this->parent->token}) is not valid!",
                    "token" => $this->parent->token,
                    "headers" => $http_response_header
                ]);
            } elseif (stripos($http_response_header[0], '404') !== false) {
                $this->parent->callableException('notFoundException', [
                    "name" => "Not found!",
                    "description" => "The requested bot probably doesn't exists (yet)",
                    "bot_id" => $this->id,
                    "headers" => $http_response_header
                ]);
            } else {
                $this->parent->callableException('commonException', [
                    "name" => "Undefined",
                    "description" => "Top.gg didn't say anything :(",
                    "bot_id" => $this->id,
                    "headers" => $http_response_header
                ]); 
            }
            return NULL;
        }
        return $data;
    }

    public function updateStats(array $body) : void {
        $data = Http::post("{$this->parent->endpoint}/bots/{$this->id}/stats", $this->parent->token, $body);
        if (!$data) {
            if (stripos($http_response_header[0], '401') !== false) {
                $this->parent->callableException('authenticationException', [
                    "name" => "Unhautorized",
                    "description" => "Unhautorized! Actual token ({$this->parent->token}) is not valid!",
                    "token" => $this->parent->token,
                    "headers" => $http_response_header
                ]);
            } elseif (stripos($http_response_header[0], '404') !== false) {
                $this->parent->callableException('notFoundException', [
                    "name" => "Not found!",
                    "description" => "The requested bot probably doesn't exists (yet)",
                    "bot_id" => $this->id,
                    "headers" => $http_response_header
                ]);
            } else {
                $this->parent->callableException('commonException', [
                    "name" => "Undefined",
                    "description" => "Top.gg didn't say anything :(",
                    "bot_id" => $this->id,
                    "headers" => $http_response_header
                ]); 
            }
        }
    }
}