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
 * The Global Ratelimits Exception class.
 * This allows for exceptions to be made when
 * the HTTP hits a rate limit for global requests.
 */
class GlobalRatelimitException
{
  /** @var mixed */
  public $message;

  /** Special throwing rules. */
  public const THROW_NONE    = 0;
  public const THROW_DEFAULT = 1;

  /**
   * Creates a GlobalRatelimitException class.
   *
   * @param   const|null $type The throwing type.
   * @return  void
   */
  public function __construct(string $message, $type = self::THROW_DEFAULT)
  {
    $this->message = "You have encountered a global ratelimit. Please refer to the JSON contents for your remaining time.";

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
