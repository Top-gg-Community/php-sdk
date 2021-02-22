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

interface Struct {
  public function __construct(string $auth);
  public function CALL(string $method, string $path, array $parameters);
}

class TopPHP implements Struct {
  // Let these represent our wrapper structure.
  private $RESP;
  private $HTTP;
  private $headers;
  private $token;

  public function __construct(string $auth) {
    /*
      Declare variables through our class construction.
    */

    $this->RESP = 0;
    $this->HTTP = "https://top.gg/api";
    $this->headers = ["http" => []];
    $this->token = $auth;
  }

  private function log(...$contents) {
    /*
      Logging events for debugging purposes.

      ->log(
        "[ERROR]",
        $err->dec("ERROR_NAME"),
        $err->type["MAJOR"]
      );
    */

    $file = fopen("log.txt", "a");
    $attach = implode(" ", $contents);

    fwrite($file, "{$attach}\n");
    fclose($file);
  }

  public function CALL(string $method,
                       string $path,
                       array $parameters) {
    /*
      Set up the structure for allowing HTTP type requests.
      This will only work in conjunction with the supported
      method of the API wrapper. It's recommended to keep
      comment structure for understanding the use.

      ->CALL(
        "GET",  # method
        "bots", # URI path
        [       # parameters
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
        "header" => "Authorization: {$this->token}",
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
}

?>
