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

class Cache {
    // Using session cache because they're fast and beautiful
    // But not for token :D
    // Check
    public static function isInit() : bool {
        if (!empty($_SESSION['topggSessionStorage'])) {
            return true;
        }
        return false;
    }

    public static function init() : void {
        $_SESSION['topggSessionStorage'] = new \stdClass;
    }

    // Add an element to cache
    public static function set(string $name, mixed $value) : void {
        if (!self::isInit()) { self::init(); }
        $_SESSION['topggSessionStorage']->{$name} = $value;
    }

    // Is this thing in cache? Idk, just ask to the code
    public static function is(string $name) : bool {
        if (!self::isInit()) { self::init(); }
        if (!empty($_SESSION['topggSessionStorage']->{$name})) {
            return true;
        }
        return false;
    }

    // Get an element from cache
    public static function get(string $name) : mixed {
        if (!self::isInit()) { self::init(); }
        if (self::is($name)) {
            return $_SESSION['topggSessionStorage']->{$name};
        }
        return false;
    }
}