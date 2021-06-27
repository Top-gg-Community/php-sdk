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

interface RequestStruct
{
  public function __construct(string $token, string $http);
  public function req(string $type, string $path = null, array $json = [], int $port = 80);

  /** Accessor methods for getting private instances. */
  public function getContents();
  public function getCache();
  public function getResponse();
}

?>
