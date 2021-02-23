<?php

/*
  Copyright (c) 2021-present fl0w, licensed under the MIT License.
  Refer to the LICENSE document in the GitHub for the full license.
*/

// We want to disable PHP internal warnings for the time being.
error_reporting("E_NONE");
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);

interface Exceptions {
  public function __construct(string $name, bool $state);
  public function resp(string $name);
}

final class TopError implements Exceptions {
  private $error_types;
  private $write_perms;
  public $error; # anyone can have it.

  public function __construct(string $name, bool $state = true) {
    /*
      Declare variables through our class construction.
    */

    $this->error_types = [
      "REQ_TYPE_FAIL",
      "HTTP_RATE_LIMIT",
      "HTTP_UNAUTH"
    ];
    $this->write_perms = $state;
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
        $err .= "\n- Your authorization key may be incorrect.";
        $err .= "\n- You may have sent incorrect parameters.";

        break;

      default:
        $err = "There isn't an error here.";
    }

    $this->error["desc"] = $err; # lazy add for code shortening.

    if($this->write_perms) die("[TopPHP] Error {$name}: {$err}");
  }
}

?>
