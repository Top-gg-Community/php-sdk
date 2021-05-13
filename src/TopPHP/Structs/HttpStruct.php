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

namespace DBL\Structs;

interface HttpStruct
{
  public function __construct(string $http, int $port = 80);
  public function call(string $type, string $path, array $payload = []);

  /** Accessor methods for getting private instances. */
  public function getHttp();
  public function getPort();
}

?>
