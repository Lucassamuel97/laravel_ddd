<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

try {
    $response = $app->handle(\Illuminate\Http\Request::create('/api/users'));
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
} catch (\Throwable $e) {
    echo "Exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
