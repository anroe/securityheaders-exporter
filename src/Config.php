<?php

declare(strict_types=1);

namespace App;

/**
 * Class Config
 * @package App
 */
class Config
{
    private string $bindAdress = '0.0.0.0';

    private int $port = 9501;

    private int $cacheExpire = 604800;

    private bool $debug = false;

    public function __construct()
    {
        if (!empty(getenv(BIND_ADDRESS))) {
            $this->bindAdress = getenv(BIND_ADDRESS);
        }
        if (!empty(getenv(PORT))) {
            $this->port = int(getenv(PORT));
        }
        if (!empty(getenv(CACHE_EXPIRE))) {
            $this->cacheExpire = int(getenv(CACHE_EXPIRE));
        }
        if (!empty(getenv(DEBUG))) {
            $this->debug = getenv(DEBUG);
        }
    }
}