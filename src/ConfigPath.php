<?php

namespace Daikon\Config;

final class ConfigPath implements ConfigPathInterface
{
    /**
     * @var string
     */
    private const WILDCARD_TOKEN = "*";

    /**
     * @var string
     */
    private const PATH_SEP = "::";

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $key;

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
    ): ConfigPathInterface {
        $delimiterPos = strpos($pathString, self::PATH_SEP);
        if ($delimiterPos === 0) {
            throw new \Exception("Initializing malformed ConfigPath: path may not start with delimiter.");
        }
        $pathParts = explode(self::PATH_SEP, $pathString);
        if (empty($pathParts)) {
            throw new \Exception("Initializing an empty ConfigPath is not supported.");
        }
        if (count($pathParts) > 3) {
            throw new \Exception("Initializing a ConfigPath with more than three segments is not supported.");
        }
        if (count($pathParts) === 2) {
            array_unshift($pathParts, $defaultScope);
        }
        if (count($pathParts) === 1) {
            array_unshift($pathParts, $defaultScope, $defaultNamespace);
        }
        return new static(...array_reverse($pathParts));
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return bool
     */
    public function hasWildcardNamespace(): bool
    {
        return $this->namespace === self::WILDCARD_TOKEN;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function hasWildcardKey(): bool
    {
        return $this->key === self::WILDCARD_TOKEN;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $pathParts = [ $this->scope, $this->namespace, $this->key ];
        return join(self::PATH_SEP, $pathParts);
    }

    /**
     * @param string $key
     * @param string $namespace
     * @param string $scope
     */
    private function __construct(string $key, string $namespace, string $scope)
    {
        if (empty($key)) {
            throw new \Exception("Trying to create ConfigPath with empty key.");
        }
        if (empty($namespace)) {
            throw new \Exception("Trying to create ConfigPath with empty namespace.");
        }
        if (empty($scope)) {
            throw new \Exception("Trying to create ConfigPath with empty scope.");
        }
        $this->scope = $scope;
        $this->namespace = $namespace;
        $this->key = $key;
    }
}
