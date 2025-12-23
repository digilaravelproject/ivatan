<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    public static function auth()
    {
        return (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createAuth();
    }
}
