<?php

REQUIRE "TopPHP.php";

$ID = 1234567890; # Bot User ID

$api = new TopGG("YOUR_BOT_TOKEN");
$api->GET->stats($ID);

?>
