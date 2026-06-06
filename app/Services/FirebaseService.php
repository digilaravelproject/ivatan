<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    public static function auth()
    {
        $credentialsPath = config('firebase.credentials');
        $credentials = json_decode(file_get_contents($credentialsPath), true);

        return (new Factory)
            ->withServiceAccount($credentials)
            ->createAuth();
    }
}
