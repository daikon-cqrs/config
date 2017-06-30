<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

final class ConfigPath implements ConfigPathInterface
{
    private const WILDCARD_TOKEN = "*";

    private const PATH_SEP = "::";

    private $scope;

    private $namespace;

    private $key;

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

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function hasWildcardNamespace(): bool
    {
        return $this->namespace === self::WILDCARD_TOKEN;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function hasWildcardKey(): bool
    {
        return $this->key === self::WILDCARD_TOKEN;
    }

    public function __toString(): string
    {
        $pathParts = [ $this->scope, $this->namespace, $this->key ];
        return join(self::PATH_SEP, $pathParts);
    }

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
