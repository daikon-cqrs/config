<?php

namespace Accordia\PhpConfig;

interface ConfigLoaderInterface
{
    /**
     * @param mixed[] $lookup_paths
     * @param string[] $lookup_patterns
     * @return mixed[]
     */
    public function load(array $lookup_paths, array $lookup_patterns): array;
}
