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

class Users {
    // Global user collection, almost useless
    protected TopPHP $parent;

    // Class handling
    function __construct(TopPHP $parent) {
        $this->parent = $parent;
    }

    public function get(string $id) : User {
        return new User(Http::get("{$this->parent->endpoint}/users/{$id}", $this->parent->token), $this->parent);
    }
}