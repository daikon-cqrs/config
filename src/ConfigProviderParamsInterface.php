<?php

namespace Accordia\PhpConfig;

interface ConfigProviderParamsInterface
{
    /**
     * @return string
     */
    public function getDefaultScope(): string;

    /**
     * @return string
     */
    public function getDefaultNamespace(): string;

    /**
     * @param ConfigPathInterface $configPath
     * @return ConfigLoaderInterface
     */
    public function getLoader(ConfigPathInterface $configPath): ConfigLoaderInterface;

    /**
     * @param ConfigPathInterface $configPath
     * @return mixed[]
     */
    public function getNamespaces(ConfigPathInterface $configPath): array;

    /**
     * @param ConfigPathInterface $configPath
     * @return mixed[]
     */
    public function getNamespace(ConfigPathInterface $configPath): array;

    /**
     * @param ConfigPathInterface $configPath
     * @return string[]
     */
    public function getLookupPatterns(ConfigPathInterface $configPath): array;
}
