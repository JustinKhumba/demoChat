<?php
$server = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errorMessage);

if ($server === false) {
    throw new UnexpectedValueException("Could not bind to socket: $errorMessage");
}

$clients = array();

for (;;) {
    $client = @stream_socket_accept($server);

    if ($client) {
        $clients[] = $client;
        $id = uniqid();
        $welcome_message = "Welcome to the chat! Your ID is: " . $id . "\n";
        fwrite($client, $welcome_message);
        broadcast($welcome_message, $client, $clients);
        while (true) {
            $input = fgets($client);
            broadcast("Client $id: $input", $client, $clients);
        }
    }
}

function broadcast($message, $sender, $clients) {
    foreach ($clients as $client) {
        if ($sender != $client) {
            fwrite($client, $message);
        }
    }
}
