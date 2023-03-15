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

class User {
    protected TopPHP $parent;
    protected array $fillable = [
        'id',
        'username',
        'discriminator',
        'avatar',
        'defAvatar',
        'bio',
        'banner',
        'social',
        'color',
        'supporter',
        'certifiedDev',
        'mod',
        'webMod',
        'admin'
    ];
    protected array $needed = [
        'id',
        'username',
        'discriminator',
        'defAvatar',
        'social',
        'supporter',
        'certifiedDev',
        'mod',
        'webMod',
        'admin'
    ];
    public string $id;

    // Initialize the object
    function __construct(object $rawuser, TopPHP $parent) {
        $this->parent = $parent;
        foreach ($this->fillable as $needed) {
            if (empty($rawuser->{$needed}) && in_array($needed, $this->needed)) {
                $this->parent->callableException('dataMissingException', [
                    "name" => "Some data are missing during the user object creation!",
                    "description" => "Some data are missing during the user object creation!",
                    "user_id" => $rawuser->id,
                    "missing_data" => $needed
                ], $this->parent);
                continue;
            }
            $this->{$needed} = $rawuser->{$needed};
        }
    }

    public function hasVoted(string $bot) : bool {
        // if the bot is in the cache it will take from it
        $data = Http::get("{$this->parent->endpoint}/bots/{$bot}/check?userId={$this->id}", $this->parent->token);
        if (!$data) {
            $this->parent->callableException('connectionException', [
                "description" => "Something went wrong during the HTTP/1.1 request to the bot info",
                "bot_id" => $this->id
            ]);
            return new \stdClass;
        }
        if ($data->voted == 1) {
            return true;
        }
        return false;
    }
}