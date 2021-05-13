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
use DBL\API\Exceptions\MissingTokenException;
use DBL\API\Exceptions\MissingStatsException;
use DBL\Structs\BaseStruct;

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
   * @var     int
   * @access  private
   */
  private $port;

  /**
   * @var     string
   * @access  protected
   */
  protected $token;

  /**
   * @var     mixed
   * @see     \DBL\API\Request
   * @access  public
   */
  public $api;

  /**
   * @var     array
   * @access  private
   */
  private $features;

  /**
   * @var     bool
   * @access  public
   */
  public $connected;

  /**
   * Creates a DBL instance.
   *
   * @param   array $parameters The parameters necessary for an established connection.
   * @param   bool  $webhook If your connection is a webhook, defaults to false.
   * @return  void
   */
  public function __construct(array $parameters, bool $webhook = false)
  {
    /**
     * There are 3 acceptable parameters:
     * - [string] token: The API token key.
     * - [array] [opt] auto_stats: If you want to automatically post stats.
     * - [bool] [opt] safety: For webserver protection and ensuring extra locks.
     *
     * This will look for if any are present and valid.
     * Also, make sure a token is present. How the fuck are you gonna use it otherwise?
     * KEKW.
     */

    error_reporting(0);

    if($parameters["auto_stats"]) $this->features["auto_stats"][0] = true;
    if($parameters["safety"]) $this->features["safety"] = true;
    if($parameters["token"]) $this->token = $parameters["token"];
    else throw new MissingTokenException();

    $this->port = 80;
    $this->api = new Request($this->token, $this->port);

    /** Proxy an HTTP request to see if it works. */
    $_response = $this->api->req("GET", "/users/242351388137488384")["status"];
    if($_response === "200") $this->connected = true;

    /** Finally do our feature checks from the parameters list. */
    if($parameters["auto_stats"])
    {
      $this->check_auto_stats(
        $parameters["auto_stats"]["url"],
        $parameters["auto_stats"]["id"]
      );
    }

    $this->check_safety();
  }

  /**
   * Checks if stats should be posted to the website automatically.
   * This can only be done for a website URL.
   *
   * @param   string  $path     The HTTP path you're using.
   * @param   int     $id       The bot ID.
   * @param   array   $values   A list of values to be automatically posted.
   * @return  void
   */
  protected function check_auto_stats(string $path, int $id, array $values)
  {
    try
    {
      if($values["shards"])       $_json["shards"]        = $values["shards"];
      if($values["shard_id"])     $_json["shard_id"]      = $values["shard_id"];
      if($values["shard_count"])  $_json["shard_count"]   = $values["shard_count"];
      if($values["server_count"]) $_json["server_count"]  = $values["server_count"];
      else throw new MissingStatsException();

      $_id = ($id) ? $id : throw new MissingStatsException();
      $_url = ($path) ? $path : throw new MissingStatsException();
      $_type = Http::BOT;
      $_request = $this->api->req("POST", "/{$_type}/{$_id}/stats", $_json)["json"];
    }

    catch(Exception $error) { echo $error; }

    finally { echo "<meta http-equiv='refresh' content='1800;URL=\"{$_url}\"' />"; }
  }

  /**
   * Checks if the person wants a safety lock on the class.
   * Basically runs a very quick magic constant to automatically
   * delete the class instance as soon as detected. (Faster way)
   *
   * @return void
   */
  protected function check_safety()
  {
    /** One last time to check. */
    if($this->features["safety"]) die();
  }

  /**
   * Shows the information from the specified type through a query search.
   *
   * @param   string  $type The search type.
   * @param   array   $json The JSON query fields, with key:val as assoc.
   * @return  array
   */
  public function show_info(string $type, array $json = []): array
  {
    switch($type)
    {
      case Http::USER:
        $_path = "users";
        break;

      case Http::BOT:
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
   * @param   string  $type The search type.
   * @param   int     $id The bot/user ID.
   * @return  array
   */
  public function find_info(string $type, int $id): array
  {
    switch($type)
    {
      case Http::USER:
        $_path = "users";
        break;

      case Http::BOT:
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
   * @param   int   $id The bot ID.
   * @return  array
   */
  public function get_votes(int $id)
  {
    return $this->api->req("GET", "/bots/{$id}/votes")["json"];
  }

  /**
   * Returns a boolean check for if a user voted for your bot.
   *
   * @param   int   $id The user Snowflake ID.
   * @return  array
   */
  public function get_user_vote(int $id): array
  {
    return $this->api->req("GET", "/bots/{$id}/check")["json"];
  }

  /**
   * Returns the statistics of the bot.
   *
   * @param   int   $id The bot ID.
   * @return  array
   */
  public function get_stats(int $id): array
  {
    return $this->api->req("GET", "/bots/{$id}/stats")["json"];
  }

  /**
   * Posts statistics to the bot's Top.gg page.
   *
   * @param   int   $id The bot ID.
   * @param   array $json The JSON query fields.
   * @return  array
   */
  public function post_stats(int $id, array $json): array
  {
    return $this->api->req("POST", "/bots/{$id}/stats", $json)["json"];
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
