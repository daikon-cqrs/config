<?php

namespace Daikon\Config;

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
     * @return string[]
     */
    public function getLocations(ConfigPathInterface $configPath): array;

    /**
     * @param ConfigPathInterface $configPath
     * @return string[]
     */
    public function getSources(ConfigPathInterface $configPath): array;
}
