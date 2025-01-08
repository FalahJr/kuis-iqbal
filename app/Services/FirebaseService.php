<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $database;

    // public function __construct()
    // {
    //     $serviceAccountPath = config('firebase.credentials.file');

    //     if (!file_exists($serviceAccountPath)) {
    //         throw new \Exception("Service account file not found at: {$serviceAccountPath}");
    //     }

    //     $firebase = (new Factory)
    //         ->withServiceAccount($serviceAccountPath)
    //         ->withDatabaseUri(config('firebase.database_url'));

    //     $this->database = $firebase->createDatabase();
    // }


    // public function __construct()
    // {
    //     $serviceAccountPath = config('firebase.credentials.file');

    //     if (!file_exists($serviceAccountPath)) {
    //         throw new \Exception("Service account file not found at: {$serviceAccountPath}");
    //     }

    //     $firebase = (new Factory)
    //         ->withServiceAccount($serviceAccountPath)
    //         ->withDatabaseUri(config('firebase.database_url'));

    //     $this->database = $firebase->createDatabase();
    // }

    public function __construct()
    {
        $serviceAccountPath = base_path('config/firebase/cbt-kuis-firebase-adminsdk-2u4yg-1686ea021d.json');

        // dd(env('FIREBASE_CREDENTIALS'));

        // Debug: Log path yang dicari
        if (!$serviceAccountPath) {
            throw new \Exception("FIREBASE_CREDENTIALS is not set in your .env file");
        }

        if (!file_exists($serviceAccountPath)) {
            throw new \Exception("Service account file not found at: {$serviceAccountPath}");
        }

        $firebase = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri('https://cbt-kuis-default-rtdb.firebaseio.com');

        $this->database = $firebase->createDatabase();
    }


    public function getReference($path)
    {
        return $this->database->getReference($path);
    }
}
