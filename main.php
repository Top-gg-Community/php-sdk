<?php

/**
 * This file is respectively apart of the TopPHP project.
 *
 * Copyright (c) 2021-present James Walston
 * Some rights are reserved.
 *
 * This copyright is subject to the MIT license which
 * fully entails the details in the LICENSE file.
 */

/**
 * This file is to serve as an example about how the library
 * is able to be used with respect to the functions of the
 * Top.gg API. Below is a sample script for how to make it
 * work.
 */

include_once __DIR__ . "/vendor/autoload.php";
use DBL\DBL;
use DBL\API\Http;

$token = @file_get_contents(".TOKEN");
$api = new DBL([
  "token" => $token
]);

?>
