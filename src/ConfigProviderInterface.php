<?php

namespace Daikon\Config;

interface ConfigProviderInterface
{
    /**
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get(string $path, $default = null);

    /**
     * @param string $path
     * @return bool
     */
    public function has(string $path): bool;
}
