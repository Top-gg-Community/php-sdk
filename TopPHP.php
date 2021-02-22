<?php

/*
  MIT License

  Copyright (c) 2021-present fl0w

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
*/

REQUIRE "Errors.php";

interface BaseStruct {
  public function __construct(string $auth);
  public function CALL(string $method, string $path, array $parameters);

  // Accessor-only methods for the GET/POST requests.
  public function GET();
  public function POST();
}

class TopGG implements BaseStruct {
  // Let these represent our wrapper structure.
  private $RESP;
  private $HTTP;
  private $headers;
  private $token;

  // Let these represent as accessor instances.
  public $GET;
  public $POST;

  public function __construct(string $auth) {
    /*
      Declare variables through our class construction.
    */

    $this->RESP = 0;
    $this->HTTP = "https://top.gg/api";
    $this->headers = ["http" => []];
    $this->token = $auth;

    $this->GET = $this->GET($auth);
    $this->POST = $this->POST($auth);
  }

  private function log(...$contents) {
    /*
      Logging events for debugging purposes.
      The log statement must be used in a conditional
      statement if the TopError() exception is passed.
      Otherwise, write to your heart's content!

      ->log(
        "[MAJOR]", # could be for more "oomph." :-)
        throw new TopError("ERROR_NAME"),
      );
    */

    $file = fopen("log.txt", "a");
    $attach = implode(" ", $contents);

    fwrite($file, "{$attach}\n");
    fclose($file);
  }

  public function CALL(string $method,
                       string $path,
                       array $parameters = []) {
    /*
      Set up the structure for allowing HTTP type requests.
      This will only work in conjunction with the supported
      method of the API wrapper. It's recommended to keep
      comment structure for understanding the use.

      ->CALL(
        "GET",  # method
        "bots", # URI path
        [       # parameters (opt)
          "limit"   => 50,
          "offset"  => 0
        ]
      );
    */

    // Make sure the method is legitimate.
    $types = ["GET", "POST"];

    if(!in_array($method, $types)) {
      $this->RESP = 401;
      throw new TopError("REQ_TYPE_FAIL");
    }

    // As long as we're not rate limited, make the HTTP request.
    if($this->RESP !== 429) {
      $this->headers["http"] = [
        "header" => "Authorization: {$this->token}\nContent-type: application/x-www-form-urlencoded",
        "method"  => $method,
        "content" => http_build_query($parameters)
      ];

      // Send the HTTP request and retrieve the contents.
      $context = stream_context_create($this->headers);
      $request = file_get_contents(
        "{$this->HTTP}/{$path}",
        false,
        $context
      );

      // Introduce error handling for failed attempts.
      if($request === false) {
        $this->RESP = 401;
        throw new TopError("HTTP_UNAUTH");
      } else {
        $this->RESP = 200;
        $request = var_dump($request);
      }

      return $request;
    }
    else throw new TopError("HTTP_RATE_LIMIT");
  }

  public function GET() {
    return new GET($this->token);
  }

  public function POST() {
    return new POST($this->token);
  }
}

class GET extends TopGG {
  /*
    All of the past Base variables involving HTTP
    inclusion data are already extended here, so
    we'll just call them as-is. This also forces us
    to use the functions of Base regardless of the
    extends Keyword since the interface is forced.

    Theoretical usage is as follows:

    $api = new TopGG("API_TOKEN");
    $api->GET->votes($ID); # ¯\_(ツ)_/¯
  */

  // Let this represent an instance for values.
  private $result;

  public function __construct(string $auth) {
    /*
      Declare variables through our class construction.
    */

    $this->result = null;
  }

  public function bots(array $query) {
    /*
      Gets a list of bots that match a specific query.
    */

    $list = [];
    $this->result = $this->CALL(
      "GET", "bots",
      [
        "search" => implode(" ", function() {
          $i = 0;
          foreach($query as $field => $value) {
            $list[$i++] = "{$field}:";
            $list[$i] = "{$value}";
          }
        })
      ]
    );

    return $this->result[0];
  }

  public function bot(int $id) {
    /*
      Finds a single bot.
    */

    $this->result = $this->CALL(
      "GET", "bot/{$id}"
    );

    return $this->result;
  }

  public function votes(int $id) {
    /*
      Gets the last 1000 voters for your bot.
    */

    $this->result = $this->CALL(
      "GET", "bots/{$id}/votes"
    );

    return $this->result[0];
  }

  public function vote(int $bot_id, int $user_id) {
    /*
      Checking whether or not a user has voted for your bot.
    */

    $this->result = $this->CALL(
      "GET", "bots/{$bot_id}/check",
      [
        "userId" => $user_id
      ]
    );

    return $this->request["voted"];
  }

  public function stats(int $id) {
    /*
      Specific stats about a bot.
    */

    $this->result = $this->CALL(
      "GET", "bots/{$id}/stats"
    );

    return $this->request;
  }
}

class POST extends TopGG {
  /*
    Following the same principle as the GET class, we
    will only extend off of Base to leech for data.

    Theoretical usage is as follows:

    $api = new Base("YOUR_TOKEN_HERE");
    $api->POST->votes; # ¯\_(ツ)_/¯
  */

  public function __construct(string $auth) {
    /*
      Declare variables through our class construction.
    */
  }
}

?>
