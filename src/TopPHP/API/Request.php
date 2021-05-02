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

namespace DBL\API;
use DBL\API\Http;

interface RequestStruct
{
  public function __construct(string $token);
  public function req(string $type, string $path = null, array $json = [], int $port = 80);

  /** Accessor methods for getting private instances. */
  public function getContents();
  public function getCache();
  public function getResponse();
}

/**
 * Represents the HTTP request class for Top.gg.
 * This defines how the GET and POST calls will
 * work in structure, as well as additional info
 * passed down to exceptions and specific func.
 */
class Request implements RequestStruct
{
  /** @var string */
  private $http;

  /** @var string */
  protected $token;

  /** @var array */
  private $content;

  /** @var string */
  private $cache;

  /** @var string */
  private $response;

  /**
   * Creates a Request class instance.
   *
   * @param string $http The HTTP path you're calling.
   * @param int $port the HTTP port you're inferring.
   * @return void
   */
  public function __construct(string $token)
  {
    $this->http = "https://top.gg/api";
    $this->token = $token;
    $this->content = ["raw" => null, "split" => []];
    $this->cache = null;
    $this->response = null;
  }

  /**
   * Defines how an HTTP request is inferred on.
   * The rule is simple: as long as a URL/URI path
   * is given and the response is OK, and path is said
   * to be valid, a request will be validated. Otherwise,
   * an exception is thrown.
   *
   * @param string $type The HTTP request you're using.
   * @param string $path The HTTP path you're calling.
   * @param array $json Additional information you want to pass on as JSON.
   * @param int $port The HTTP port you're inferring.
   * @return array
   */
  public function req(string $type, string $path = null, array $json = [], int $port = 80): array
  {
    $_http = new Http($this->http, $port);
    $_path = (!empty($path)) ? $_http->getHttp() . $path : null;
    $_error = false;
    $_request = null;
    $_response = null;

    try
    {
      /**
       * Set up the HTTP request structure.
       * Will contextualize and create how we will interact.
       */
      $_struct = @stream_context_create([
        "http" => [
          "method" => $type,
          "header" => "Content-Type: application/json" . "\r\n" .
                      "Authorization: {$this->token}" . "\r\n"
        ]
      ]);

      /**
       * Here is where the official request is made
       * to receive information.
      */
      $_request = @file_get_contents($_path, true, $_struct);
      if(!$_request) $_error = true;
    }

    catch (Exception $error) { return $error->getMessage(); }

    finally
    {
      if(!$_error)
      {
        header("Content-Type: application/json");

        $_struct = $_http->call(
          $type,
          $_path,
          json_decode($_request, true)
        );
        $this->cache = $_struct;

        return $_struct;
      }
      else
      {
        error_reporting(E_ALL);
        $_error = error_get_last();

        /**
         * We'll need to manually pull the response
         * status code for this when the error comes.
         */
        $_headers = get_headers($_path);
        $this->response = $_response = substr($_headers[0], 9, 3);

        return
        [
          "type" => $type,
          "path" => $_path,
          "status" => $_response,
          "json" => ["message" => $_error["message"]]
        ];
      }
    }
  }

  /**
   * Gets the HTTP webpage's contents.
   *
   * @return array
   */
  public function getContents(): array
  {
    return $this->content;
  }

  /**
   * Gets the cache of the last requested interaction.
   * It is recommended to save this value into an
   * instance when being used for preservation.
   *
   * @return string
   */
  public function getCache(): string
  {
    return $this->cache;
  }

  /**
   * Gets the HTTP response status.
   *
   * @return string
   */
  public function getResponse(): string
  {
    return $this->response;
  }
}

?>
