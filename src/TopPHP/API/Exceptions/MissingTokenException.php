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
 * The Missing Token Exception class.
 * This allows for an exception to be made when
 * the DBL class does not detect a token.
 */
class MissingTokenException
{
  /** @var mixed */
  public $message;

  /** Special throwing rules. */
  public const THROW_NONE    = 0;
  public const THROW_DEFAULT = 1;

  /**
   * Creates a MissingTokenException class.
   *
   * @param   const|null $type The throwing type.
   * @return  void
   */
  public function __construct($type = self::THROW_DEFAULT)
  {
    $this->message = "The API could not make a successful connection due to a missing token. (Do you have a token path specified in the DBL class?)";

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
