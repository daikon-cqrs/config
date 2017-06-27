<?php

namespace Daikon\Config;

interface ConfigLoaderInterface
{
    /**
     * @param string[] $locations
     * @param string[] $sources
     * @return mixed[]
     */
    public function load(array $locations, array $sources): array;

    /**
     * @param mixed[] $config
     * @return string
     */
    public function serialize(array $config): string;

    /**
     * @param string $serializedConfig
     * @return mixed[]
     */
    public function deserialize(string $serializedConfig): array;
}
