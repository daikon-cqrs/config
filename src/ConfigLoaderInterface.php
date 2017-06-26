<?php

namespace Daikon\Config;

interface ConfigLoaderInterface
{
    /**
     * @param mixed[] $lookup_paths
     * @param string[] $lookup_patterns
     * @return mixed[]
     */
    public function load(array $lookup_paths, array $lookup_patterns): array;

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
