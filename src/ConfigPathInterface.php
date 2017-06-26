<?php

namespace Daikon\Config;

interface ConfigPathInterface
{
    /**
     * @param string $pathString
     * @param string $defaultScope
     * @param string $defaultNamespace
     * @return ConfigPathInterface
     */
    public static function fromPathString(
        string $pathString,
        string $defaultScope,
        string $defaultNamespace
    ): ConfigPathInterface;

    /**
     * @return string
     */
    public function getScope(): string;

    /**
     * @return string
     */
    public function getNamespace(): string;

    /**
     * @return bool
     */
    public function hasWildcardNamespace(): bool;

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @return bool
     */
    public function hasWildcardKey(): bool;

    /**
     * @return string
     */
    public function __toString(): string;
}
