<?php
/**
 * This file is respectively apart of the TopPHP project (specifically of the TopPHP/Webhooks project).
 *
 * Copyright (c) 2022-present Federico Cosma
 * Some rights are reserved.
 *
 * This copyright is subject to the MIT license which
 * fully entails the details in the LICENSE file.
 * This library use ReactPHP (react\http) libraries for non stopping http webserver for websockets. https://reactphp.org
 * ReactPHP is under the MIT license
 */

namespace TopPHP\Parts\Webhooks;

class EventLoop {
    protected array $eventList = [
        'onvote'
    ]; // USELESS because there's only one event now uwu
    protected object $reactions;
    protected string $auth;
    protected object $errorHandlers;

    // Create element
    function __construct(object $reactions, object $errorHandlers, string $authToken = NULL) {
        $this->reactions = $reactions;
        $this->auth = $authToken;
        $this->errorHandlers = $errorHandlers;
    }

    public function callErrorHandler(string $name, mixed $data) : void {
        echo "calling error handl\n";
        ($this->errorHandlers->{$name})($data);
    }

    public function callReturnHandler(string $name, mixed $data) : void {
        echo "calling success handl\n";
        ($this->reactions->{$name})->mainless($data);
    }
    
    public function start(string $server = "0.0.0.0:8081") : void {
        $class = $this;
        $socket = new \React\Socket\SocketServer($server);
        $http = new \React\Http\HttpServer(function (\Psr\Http\Message\ServerRequestInterface $request) use ($class) {
            echo "Result\n";
            if (!empty($request->getHeaders()['Authorization']) && $request->getHeaders()['Authorization'][0] === $class->auth) {
                // Only one event so skip event check and let's give results
                echo "correct";
	        	var_dump($class);
                $class->callReturnHandler('onvote', $request->getParsedBody());
            } else {
                echo "bit error";
		        var_dump($request->getHeaders());
                $class->callErrorHandler('unhautorized', $request);
            }
            return \React\Http\Message\Response::plaintext(
               "Hello World!\n"
            );
        });
        $http->listen($socket);
	    echo "Listening on {$server}\n";
    }
}