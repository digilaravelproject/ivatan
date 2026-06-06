<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

try {
    $credentialsPath = config('firebase.credentials');
    echo "Credentials path: $credentialsPath\n";
    echo "File exists: " . (file_exists($credentialsPath) ? 'YES' : 'NO') . "\n";
    
    $serviceAccount = ServiceAccount::fromJsonFile($credentialsPath);
    $factory = (new Factory())->withServiceAccount($serviceAccount);
    $messaging = $factory->createMessaging();
    echo 'FCM: SUCCESS' . PHP_EOL;
} catch (\Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo 'TRACE: ' . $e->getTraceAsString() . PHP_EOL;
}