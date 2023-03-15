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

class Collection {
    protected object $collector;

    function __construct(object $existingCollector = null) {
        if ($existingCollector != null) {
            $this->collector = $existingCollector;
        } else {
            $this->collector = new \stdClass;
        }
    }
    
    public function set(string $key, mixed $value) : void {
        $this->collector->{$key} = $value;
    }

    public function add(string $key, mixed $value) : void {
        self::set($key, $value);
    }

    public function get(string $key) : mixed {
        return $this->collector->{$key};
    }

    public function remove(string $key) : void {
        $this->collector->{$key} = null;
    }

    public function count() : int {
        return count($this->collector);
    }

    public function index() : array {
        $data = [];
        foreach ($this->collector as $key => $value) {
            array_push($data, $key);
        }
        return $data;
    }

    public function foreach(callable $callback) : void {
        foreach ($this->collector as $key => $value) {
            $callback($key, $value);
        }
    }

    public function all() : object {
        return $this->collector;
    }
}