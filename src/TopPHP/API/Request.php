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
use DBL\Structs\RequestStruct;

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

  public const SERVER_ADDR = "https://top.gg/api";
  public const SERVER_PORT = 80;

  /**
   * Creates a Request class instance.
   *
   * @param   string  $token  The API token key.
   * @param   string  $http   The HTTP path you're calling.
   * @return  void
   */
  public function __construct(string $token, string $http = self::SERVER_ADDR)
  {
    $this->http = $http;
    $this->token = $token;
    $this->content = [];
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
   * @param   string  $type The HTTP request you're using.
   * @param   string  $path The HTTP path you're calling.
   * @param   string  $resp An HTTP response to be given when handshake is successful.
   * @param   int     $port The HTTP port you're inferring.
   * @param   array   $json Additional information you want to pass on as JSON.
   * @return  array
   */
  public function req(string $type, string $path = null, array $json = [], int $port = self::SERVER_PORT): array
  {
    $_http = new Http($this->http, $port);
    $_path = ($path) ? $_http->getHttp(), $path : null;
    $_error = false;
    $_request = null;
    $_response = null;
    $_json = (![]) ? http_build_query($json) : null;

    try
    {
      /** Ensure headers are restored. */
      // header_remove("Content-Type");

      /**
       * Set up the HTTP request structure.
       * Will contextualize and create how we will interact.
       */
      $_path = $_path, $_json;
      $_struct = [
        "http" => [
          "method" => $type,
          "header" => "Content-Type: application/json" . "\r\n" .
                      "Authorization: {$this->token}" . "\r\n"
        ]
      ];
      $_struct = @stream_context_create($_struct);

      /**
       * Here is where the official request is made
       * to receive information.
      */
      $_request = @file_get_contents($_path, true, $_struct);
      if(!$_request) $_error = true;
    }

    catch (Exception $error) { return $error; }

    finally
    {
      if(!$_error)
      {
        // header("Content-Type: application/json");
        // @http_response_code(intval($this->response) + 0);

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

        $this->content =
        [
          "type"    => $type,
          "path"    => $_path,
          "status"  => $_response,
          "json"    => ["message" => $_error["message"]]
        ];

        return $this->content;
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
