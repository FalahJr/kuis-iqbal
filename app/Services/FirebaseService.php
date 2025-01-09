<?php

namespace App\Services;

use GuzzleHttp\Client;

class FirebaseService
{
    protected $client;
    protected $databaseUrl;
    protected $databaseSecret;

    public function __construct()
    {
        $this->databaseUrl = env('FIREBASE_DATABASE_URL'); // Ambil URL dari .env
        $this->databaseSecret = env('FIREBASE_DATABASE_SECRET'); // Ambil secret dari .env

        if (!$this->databaseUrl || !$this->databaseSecret) {
            throw new \Exception('FIREBASE_DATABASE_URL or FIREBASE_DATABASE_SECRET is missing in your .env file');
        }

        $this->client = new Client();
    }

    public function getReference($path)
    {
        $url = "{$this->databaseUrl}/{$path}.json?auth={$this->databaseSecret}";

        try {
            $response = $this->client->get($url);
            $body = $response->getBody();
            $data = json_decode($body, true);

            return $data;
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch data from Firebase: " . $e->getMessage());
        }
    }

    // public function setReference($path, $data)
    // {
    //     $url = "{$this->databaseUrl}/{$path}.json?auth={$this->databaseSecret}";

    //     try {
    //         $response = $this->client->put($url, [
    //             'json' => $data
    //         ]);
    //         $body = $response->getBody();
    //         return json_decode($body, true);
    //     } catch (\Exception $e) {
    //         throw new \Exception("Failed to set data to Firebase: " . $e->getMessage());
    //     }
    // }

    public function setReference($path, $data)
    {
        $url = "{$this->databaseUrl}/{$path}.json?auth={$this->databaseSecret}";

        try {
            // Gunakan metode DELETE jika data adalah null
            if ($data === null) {
                $response = $this->client->delete($url);
            } else {
                $response = $this->client->put($url, [
                    'json' => $data
                ]);
            }

            $body = $response->getBody();
            return json_decode($body, true);
        } catch (\Exception $e) {
            throw new \Exception("Failed to set data to Firebase: " . $e->getMessage());
        }
    }
}
