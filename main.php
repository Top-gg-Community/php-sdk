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

$token = "YOUR TOP.GG API TOKEN KEY HERE.";
$api = new DBL([
  "token" => $token
]);

if($api->connected)
{
  print_r($api->get_user_vote(799697654279307314, 242351388137488384));
}

?>
