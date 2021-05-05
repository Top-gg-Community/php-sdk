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

namespace DBL;
use DBL\API\Http;
use DBL\API\Request;
use DBL\API\RequestType;
use DBL\API\SearchType;

interface BaseStruct
{
  public function __construct(array $parameters, bool $webhook = false);

  /**
   * GET requests are shown respectively here.
   * These use customized formatting for different
   * procedures.
   */
  /** Show statistics for all bots/users or specified. */
  public function show_info(string $type, array $json = []);
  public function find_info(string $type, int $id);

  /** Get information on the votes, vote check; and stats. */
  public function get_votes(int $id);
  public function get_user_vote(int $id);
  public function get_stats(int $id);

  /**
   * POST requests will be handled here, and have to be
   * taken into account differently due to their nature.
   */
  public function post_stats(int $id, array $json);

  /** Accessor methods for private instances. */
  public function getHttp();
  public function getPort();
  public function getContents();
  public function getCache();
  public function getResponse();
}

/**
 * Represents the TopPHP/Top.gg base class.
 * This class handles all of the specified
 * GET and POST requests that the API allows
 * to be called on, and has methods declared
 * for each specific/particular usage.
 */
final class DBL implements BaseStruct
{
  /**
   * @var int
   * @access private
   */
  private $port;

  /**
   * @var string
   * @access protected
   */
  protected $token;

  /**
   * @var mixed
   * @see \DBL\API\Request
   * @access public
   */
  public $api;

  /**
   * @var bool
   * @access public
   */
  public $connection;

  /**
   * Creates a DBL instance.
   *
   * @return void
   */
  public function __construct(array $parameters, bool $webhook = false)
  {
    $this->token = $parameters["token"];
    $this->port = 80;
    $this->api = new Request($this->token, $this->port);

    /** Proxy HTTP request to see if it works. */
    $response = $this->api->req("GET", "/users/242351388137488384")["status"];
    if($response === "200") $this->connected = true;
  }

  /**
   * Shows the information from the specified type through a query search.
   *
   * @param string $type The search type.
   * @param array $json The JSON query fields, with key:val as assoc.
   * @return array
   */
  public function show_info(string $type, array $json = []): array
  {
    switch($type)
    {
      case SearchType::USER:
        $_path = "users";
        break;

      case SearchType::BOT:
        $_path = "bots";
        break;

      default:
        die("Invalid search parameter: {$type}");
        break;
    }

    return $this->api->req("GET", "/{$type}", $json)["json"];
  }

  /**
   * Displays the general information about something
   * given through the search type.
   *
   * @param string $type The search type.
   * @param int $id The bot/user ID.
   * @return array
   */
  public function find_info(string $type, int $id): array
  {
    switch($type)
    {
      case SearchType::USER:
        $_path = "users";
        break;

      case SearchType::BOT:
        $_path = "bots";
        break;

      default:
        die("Invalid search parameter: {$type}");
        break;
    }

    return $this->api->req("GET", "/{$type}/{$id}")["json"];
  }

  /**
   * Returns the total votes of the bot.
   *
   * @param int $id The bot ID.
   * @return array
   */
  public function get_votes(int $id)
  {
    return $this->api->req("GET", "/bots/{$id}/votes")["json"];
  }

  /**
   * Returns a boolean check for if a user voted for your bot.
   *
   * @param int $id The user Snowflake ID.
   * @return array
   */
  public function get_user_vote(int $id): array
  {
    return $this->api->req("GET", "/bots/{$id}/check")["json"];
  }

  /**
   * Returns the statistics of the bot.
   *
   * @param int $id The bot ID.
   * @return array
   */
  public function get_stats(int $id): array
  {
    return $this->api->req("GET", "/bots/{$id}/stats")["json"];
  }

  /**
   * Posts statistics to the bot's Top.gg page.
   *
   * @param int $id The bot ID.
   * @param array $json The JSON query fields.
   * @return array
   */
  public function post_stats(int $id, array $json): array
  {
    $_request = new Request($this->token);

    return $_request->req("POST", "/bots/{$id}/stats", $json)["json"];
  }

  /**
   * Returns the current HTTP address.
   *
   * @return string
   */
  public function getHttp(): string
  {
    return $this->http;
  }

  /**
   * Returns the current HTTP port serial identification.
   *
   * @return int
   */
  public function getPort(): int
  {
    return $this->port;
  }

  /**
   * Returns the current HTTP request.
   *
   * @return array
   */
  public function getContents(): array
  {
    return $this->api->getContents();
  }

  /**
   * Returns the last parsed HTTP request.
   *
   * @return array
   */
  public function getCache(): array
  {
    return $this->api->getCache();
  }

  /**
   * Returns the current HTTP response code.
   * (Not to be confused with the cached version in getCache())
   *
   * @return string
   */
  public function getResponse(): string
  {
    return $this->api->getResponse();
  }
}

?>
