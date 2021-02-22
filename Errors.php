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

interface Exceptions {
  public function __construct(string $name);
  public function resp(string $name);
}

final class TopError implements Exceptions {
  private $error_types;
  public $error; # anyone can have it.

  public function __construct(string $name) {
    /*
      Declare variables through our class construction.
    */

    $this->error_types = [
      "REQ_TYPE_FAIL",
      "HTTP_RATE_LIMIT",
      "HTTP_UNAUTH"
    ];
    $this->error = [
      "type" => "N/A",
      "desc" => "N/A"
    ];

    // Pass it off with a response if it exists.
    if(in_array($name, $this->error_types)) $this->resp($name);
  }

  public function resp(string $name) {
    /*
      Set up a switch-case library of all possible errors.
      Should organize a LITTLE better than throwing it all
      into an associative array.
    */

    // Do some quick logging so anyone can get details.
    $this->error["type"] = $name;
    $err = null;

    switch($name) {
      case "REQ_TYPE_FAIL":
        $err = "This method is not supported. (Must be GET/POST type)";
        break;

      case "HTTP_RATE_LIMIT":
        $err = "You have exceeded the API rate limit. (Timeout 1 hour)";
        break;

      case "HTTP_UNAUTH":
        $err = "The HTTP request failed due to unauthorized access.";
        $err = $err . "\n- Your authorization key may be incorrect.";
        $err = $err . "\n- You may have sent incorrect parameters.";

        break;

      default:
        die("There isn't an error here.");
    }

    $this->error["desc"] = $err; # lazy add for code shortening.

    die("[ERROR] {$name}: {$err}");
  }
}

?>
