<?php

REQUIRE "TopPHP.php";

$ID = 1234567890; # Bot User ID

$api = new TopGG("aa");
$api->GET->stats($ID);

?>
