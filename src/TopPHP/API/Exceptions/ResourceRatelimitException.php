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

namespace DBL\API\Exceptions;

/**
 * The Resource-specific Ratelimits Exception class.
 * This allows for exceptions to be made when
 * the HTTP hits a rate limit for a specific request.
 */
class ResourceRatelimitException
{
  /** @var mixed */
  public $message;

  /** Special throwing rules. */
  public const THROW_NONE    = 0;
  public const THROW_DEFAULT = 1;

  /**
   * Creates a ResourceRatelimitException class.
   *
   * @param   string      $message  The error message.
   * @param   const|null  $type     The throwing type.
   * @return  void
   */
  public function __construct(string $message, $type = self::THROW_DEFAULT)
  {
    $this->message = $message;

    switch($type)
    {
      case self::THROW_NONE:
        break;

      default:
        die($this->message);
        break;
    }
  }
}

?>
