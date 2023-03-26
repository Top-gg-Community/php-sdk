# TopPHP - Documentation
Through this library you can quickly interact with the top.gg API. 

## Bot object
TopPHP handles bots as objects, so that all functions related to a bot are grouped in a single place
#### `TopPHP\Components\Bot`
---
### Getting the bot object
You can get the bot object by `get` and the `bots` repository:
```php
$bot = $topphp->bots->get(string $botid);
```
**Example:**
```php
$bot = $topphp->bots->get('123123123123123');
```
---
### Get bot's stats
```php
$stats = $bot->stats();   : object
```
It will be an object with [api stats](https://docs.top.gg/api/bot/#bot-stats)

---
### Get last 1000 votes
```php
$votes = $bot->votes();   : array
```
An array with all 1000 users who have voted the bot.
It will not load every user with the `User` object due to rate-limit.
[Array values](https://docs.top.gg/api/bot/#last-1000-votes)

---
### Individual user vote
**TopPHP** has implemented various aliases for this function:
```php
$bot->hasBeenVotedBy(string $userid)   : bool
$bot->hasBeenVoted(string $userid)   : bool
$bot->votedBy(string $userid)   : bool
```
You can also check if an user has voted a bot with the User object

---
### Update stats
You can also update the bot's stats with TopPHP
```php
$bot->updateStats(array $data)   : void
```
**Example:**
```php
$bot->updateStats([
    "server_count" => 3500,
    "shard_count" => 2
]);
```
Array `$data` must follow the [documentation](https://docs.top.gg/api/bot/#post-body)

---
### Bot information
In the `Bot` object are saved all the bot's informations.<br>
The complete list is in the [official documentation](https://docs.top.gg/api/bot/#bot-structure)

<br>


## User object
As with bots, all of a user's information is saved and provided in the form of an object as it is easier to access the various functions.

`TopPHP\Components\User`

### Getting the user object
You can get the user object by `get` and the `users` repository:
```php
$user = $topphp->users->get(string $userid);
```
**Example:**
```php
$user = $topphp->users->get('123123123123123');
```
---
### Check if the user has voted a bot
This function for completeness can also be called from the user object:
```php
$user->hasVoted(string $botid)   : bool
```
---
### User information
In the `User` object are saved all the user's informations.<br>
The complete list is in the [official documentation](https://docs.top.gg/api/users/#structure)

<br>

## Guild Object
It will now also be possible to retrieve Server information thanks to this library!
> **Warning**
> These APIs are not officially supported for the library's use, so we recommend using them with caution.

`TopPHP\Components\Guild`

### Getting the guild object
You can get the user object by `get` and the `users` repository.
> **Warning**
> To retrieve information on a server, it is mandatory to enter the full name of the server in addition to the ID!
```php
$guild = $topgg->guilds->get(string $name, string $id)  : Guild;
```
Example:
```php
$guild = $topgg->guilds->get('Foundation', '775487804540190780')  : Guild;
```
Sometimes the server may not be found due to various problems, in which case a `notFoundException` will be invoked.

---
You can also get the top guilds on top.gg:
```php
$top = $topphp->guilds->top()  : Collection
```
You get a list of servers in a Collection (associative array) that has the server ID as key.
Example:
```php
$foundation = $topphp->guilds->top()->get('775487804540190780');
```
---
### Guild information
In the `Guild` object are saved all the Guild's informations.<br>
Here a complete list: (`?` = not required):
* `id`
* `type`
* `platform`
* `name`
* `icon`
* `votes`
* `nsfwLevel`
* `description`
* `?tags`
* `?socialCount`
* `isLocked`
* `?lockAuthor`
* `?lockReason`
* `createdAt`
* `?reviewStats`
* `iconUrl`
* `?reviewScore`

<br>


## Webhook management
**TopPHP** has an internal library for complete webhook management.
In order to use this library, you must first install [`reactphp/http`](https://github.com/reactphp/http) via composer.

### Webhook configuration
The initial class (`TopPHP\TopPHP`) already provides an interface to use the Webhook object but without configuration, so these are the values that will need to be configured manually:
* `auth` - The **Authorization** key
* `ip:port` - The webserver's IP and port. `0.0.0.0` is reachable from outside the server! - Default: `0.0.0.0:8081`<br>
---
### Getting the webhook object
```php
$webhook = $topgg->webhook
```
but you can also use directly `$topgg->webhook`

---
### Configuration of Authorization key
You need to define your authorization key:
```php
$webhook->auth = 'omgthisismybeautifultop.ggsecretkey';
```

### Exceptions management
See the **Exceptions** chapter

### Event listener configuration
With the `Webhook` object you can (and need to) define the callback functions for all the events.<br>
Because top.gg is beautiful now we have only one event:
* `onvote`

You can create a listener with the `addEventListener` function:
```php
$webhook->addEventListener('onvote', function($data) {
    // ...
});
```
Data is an array/object with [these informations](https://docs.top.gg/resources/webhooks/#bot-webhooks)

---
### Starting the client
To start the React WebServer, you'll just need to use this function after configuring everything:
```php
$webhook->client()->start();
```

If you want to use another port than `8081` or if you want to run the webserver locally (why???) you can add an argumento to the `start()` function:
```php
$webhook->client()->start('0.0.0.0:1234');
```
ReactPHP is non-blocking so you can run other code after the WebServer!

## Exceptions management
**TopPHP** handles exceptions internally and you can add callbacks or not for each various error.<br>
There's a list of all errors:
* `dataMissingException`
* `connectionException`
* `authenticationException`
* `rateLimitException` [X]
* `libraryMissingException`
* `notFoundException`
* `commonException`

### Basic exception structure
Usually exceptions follow this pattern (array):
```php
[
    "name" => "Connection exception!",
    "description" => "Let me describe this annoying exception"
]
```
> **Note**
> The `name` field is not present in all exceptions!

---
### `dataMissingExceptions`
One or more required data were not sent by the top.gg API.<br>
**Default structure:**
```php
[
    "name" => string,
    "description" => string,
    "bot_id" => string,
    "missing_data" => string
]
```
**Called by:**
* `TopPHP\Components\User`
* `TopPHP\Components\Bot`

**Cause:**
> A data element contained in the `$needed` array was not provided by the API

---
### `connectionException`
The top.gg API did not respond in a maximum time of 10 seconds.<br>
Does not include authentication error.
**Default structure:**
```php
[
    "description" => string,
    "bot_id" / "user_id" => string,
    "headers" => array
]
```
**Called by:**
* `TopPHP\Components\User`
* `TopPHP\Components\Users`
* `TopPHP\Components\Bot`
* `TopPHP\Components\Bots`

**Cause:**
> The response did not arrive in the maximum time, probably due to a service downtime

---
### `authenticationExceptions`
The access token was invalid.<br>
The check is not done in all endpoints but only in the following:
* `/bots/:id/votes` 
* `/bots/:id/stats` (POST)
* `/bots`
* `/bots/:id`

**Default structure:**
```php
[
    "name" => string,
    "description" => string,
    "token" => string,
    "headers" => string
]
```
**Called by:**
* `TopPHP\Components\Bot`
* `TopPHP\Components\Bots`


**Cause:**
> The token provided in the `Authorization` header was invalid

---
### `rateLimitException`
Not yet implemented as it is not possible to easily find out how many requests are missing to be rate-limited without going too heavy on the library.
This could be implemented in the future

---
### `libraryMissingExceptions`
A requested library was not found
**Default structure:**
```php
[
    "name" => string,
    "description" => string,
    "lib" => string
]
```
**Called by:**
* `TopPHP\Parts\Webhooks\Webhook`

**Cause:**
> Some library required with composer was not found (for specific functions).

---
### `notFoundException`
Something has not been found
**Default structure:**
```php
[
    "name" => string,
    "description" => string,
    "bot_id" / "user_id" => string,
    "headers" => array
]
```
**Called by:**
* `TopPHP\Components\Bot`
* `TopPHP\Components\Bots`

**Cause:**
> A nonexistent page was requested, probably the bot ID is wrong

---
### `commonException`
Something went wrong but even the program doesn't really know what.
**Default structure:**
```php
[
    "description" => string
]
```
**Called by:**
* `all`

**Cause:**
> Undefined

---
### Managing exceptions
Now we can see how to handle each of these exceptions individually.<br>
> **Warning**
> The functions for exception handling should be done just after the creation of the `TopPHP` base class!

```php
// Managing an connectionException
$topphp->exceptionHandler('connectionException', function($data) {
    echo "New connection error!\nHeaders: " . $data['headers'];
    exit;
});
```

You can handle all exceptions with separate functions and you can also override the functions!


---
### Managing Webhooks exceptions
Webhooks include another exception handling system since they aim to be a differentiated part from the rest of the library, and providing the class with its own exception handler is a step toward autonomy.<br>
Exceptions are handled in the same way as normal exceptions, only the function name changes and the `Webhook` object is needed.
Currently, the only event that can be handled is the `unhauthorized`:
```php
$webhook->addErrorHandler('unhautorized', function($detail) {
    // ....
});
```

## Caching
TopPHP, wanting to be library-free in the basic version handles caches with sessions.
Therefore to allow the caches to start properly you need to use the `session_start()` function at the top of the code

## Main class configuration
It is possible to configure some values already starting from the main class in order to make the library do different things.<br>
This is the complete configuration array:
```php
[
    "token" => "omgmybeautifultop.ggsecrettokenhereuwu",
    "loadAllData" => false
]
```
**What is the `loadAllData` function?**
> Activating the loadAllData function when a bot is requested will automatically render the list of authors as a list of `User` objects (more requests btw)



## Code examples
### Getting a bot's prefix
```php
session_start();

require 'vendor/autoload.php';
use TopPHP\TopPHP;

$topphp = new TopPHP([
    "token" => "abcdefghiuwu",
    "loadAllData" => false,
]);

$prefix = $topphp->bots->get('123123123123123')->prefix;
```

---
### Check if an user has voted the bot
#### From `TopPHP\Components\Bot`
```php
session_start();

require 'vendor/autoload.php';
use TopPHP\TopPHP;

$topphp = new TopPHP([
    "token" => "abcdefghiuwu",
    "loadAllData" => false,
]);

if ($topphp->bots->get('1234412223122321')->hasBeenVotedBy('12231231231233132')) {
    echo "User 12231231231233132 has voted!";
}
```
#### From `TopPHP\Components\User`
```php
session_start();

require 'vendor/autoload.php';
use TopPHP\TopPHP;

$topphp = new TopPHP([
    "token" => "abcdefghiuwu",
    "loadAllData" => false,
]);

if ($topphp->users->get('12231231231233132')->hasVoted('1234412223122321')) {
    echo "User 12231231231233132 has voted!";
}
```
### Get bot stats
```php
session_start();

require 'vendor/autoload.php';
use TopPHP\TopPHP;

$topphp = new TopPHP([
    "token" => "abcdefghiuwu",
    "loadAllData" => false,
]);

var_dump($topphp->bots->get('1234412223122321')->stats());
```
### Handle exception
```php
session_start();

require 'vendor/autoload.php';
use TopPHP\TopPHP;

$topphp = new TopPHP([
    "token" => "abcdefghiuwu",
    "loadAllData" => false,
]);

$topphp->exceptionHandler('authenticationException', function($info) {
    echo "Authentication error!";
    exit;
});

var_dump($topphp->bots->get('1234412223122321')->stats());
```
### Using webhooks while handling errors
```php
session_start();

require 'vendor/autoload.php';
use TopPHP\TopPHP;

$topphp = new TopPHP([
    "token" => "abcdefghiuwu",
    "loadAllData" => false,
]);

$webhook = $topphp->webhook;
$webhook->auth = 'pizzanapoletana';
$webhook->addErrorHandler('authentication', function($info) {
    echo "Authentication error, Top.gg or someone else used the wrong auth key!\n";
});
$webhook->addEventListener('onvote', function($data) {
    echo $data['user'] . " just voted!\n";
});
$webhook->client()->start();
```