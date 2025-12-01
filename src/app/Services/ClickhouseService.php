<?php

namespace App\Services;

use ClickHouseDB\Client;
use Exception;

class ClickhouseService
{
    protected Client $client;

    public function __construct()
    {
        $config = [
            'host'     => env('CH_HOST', 'clickhouse'),
            'port'     => env('CH_PORT', 8123),
            'username' => env('CH_USER', 'default'),
            'password' => env('CH_PASSWORD', ''),
        ];

        $this->client = new Client($config);
        $this->client->database(env('CH_DB', 'default'));

        // // Recommended settings
        // $this->client->setTimeout(2);      // connect timeout
        // $this->client->setConnectTimeOut(2);
        // $this->client->setReadTimeout(5);  // read timeout
    }

    /**
     * SAFE SELECT — automatically returns rows()
     */
    public function query(string $sql): array
    {
        try {
            return $this->client->select($sql)->rows();
        } catch (Exception $e) {
            return [
                "error"   => true,
                "message" => $e->getMessage(),
            ];
        }
    }

    /**
     * RAW QUERY — return full response for debugging
     */
    public function queryRaw(string $sql)
    {
        return $this->client->select($sql);
    }

    /**
     * Escape array for IN (...)
     */
    public function escapeArray(array $values): string
    {
        return implode(
            ",",
            array_map(fn($v) => "'" . addslashes($v) . "'", $values)
        );
    }
}
