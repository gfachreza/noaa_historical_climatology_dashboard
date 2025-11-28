<?php

namespace App\Services;

use ClickHouseDB\Client;

class ClickhouseService
{
    protected Client $client;

    public function __construct()
    {
        $config = [
            'host' => env('CH_HOST', 'clickhouse'),
            'port' => env('CH_PORT', 8123),
            'username' => env('CH_USER', 'default'),
            'password' => env('CH_PASSWORD', ''),
        ];

        $this->client = new Client($config);

        $this->client->database(env('CH_DB', 'default'));
    }

    public function query(string $sql)
    {
        return $this->client->select($sql)->rows();
    }
}
