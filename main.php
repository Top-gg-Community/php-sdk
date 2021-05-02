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

$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6Ijc5OTY5NzY1NDI3OTMwNzMxNCIsImJvdCI6dHJ1ZSwiaWF0IjoxNjE5MzExNjcwfQ.ap0eil9X4M5GzmkyXXIke9rKv7QshFSE_vou0ROP5mM";
$api = new DBL([
  "token" => $token
]);

if($api->connected)
{
  $call = $api->get_votes(815774550507126824);

  print_r($call);
}

?>
