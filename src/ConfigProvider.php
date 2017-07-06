<?php
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

use Assert\Assertion;

final class ConfigProvider implements ConfigProviderInterface
{
    private const INTERPOLATION_PATTERN = '/(\$\{(.*?)\})/';

    private $config;

    private $params;

    private $preLoadInterpolations;

    public function __construct(ConfigProviderParamsInterface $params)
    {
        $this->params = $params;
        $this->config = [];
        $this->preLoadInterpolations = [];
    }

    public function get(string $path, $default = null)
    {
        $configPath = ConfigPath::fromString($path);
        $scope = $configPath->getScope();
        Assertion::keyNotExists(
            $this->preLoadInterpolations,
            $scope,
            'Recursive interpolations are not allowed when interpolating "locations" or "sources". '.
            sprintf('Trying to recurse into scope: "%s"', $scope)
        );
        if (!isset($this->config[$scope]) && $this->params->hasScope($scope)) {
            $this->config[$scope] = $this->loadScope($scope);
        } elseif (!isset($this->config[$scope])) {
            return $default;
        }
        return $this->resolvePath($configPath) ?? $default;
    }

    public function has(string $path): bool
    {
        return $this->get($path) !== null;
    }

    private function loadScope(string $scope)
    {
        $this->preLoadInterpolations[$scope] = true;
        $locations = $this->params->getLocations($scope);
        $sources = $this->params->getSources($scope);
        $loader = $this->params->getLoader($scope);
        if (!$loader instanceof ArrayConfigLoader) {
            $sources = $this->interpolateConfigValues($sources);
        }
        $locations = $this->interpolateConfigValues($locations);
        unset($this->preLoadInterpolations[$scope]);

        $this->config[$scope] = $loader->load($locations, $sources);
        return $this->interpolateConfigValues($this->config[$scope]);
    }

    private function resolvePath(ConfigPathInterface $path)
    {
        $pathPos = 0;
        $pathLen = $path->getLength();
        $pathParts = $path->getParts();
        $value = &$this->config[$path->getScope()];
        while (!empty($pathParts)) {
            $pathPos++;
            $pathPart = array_shift($pathParts);
            if (!isset($value[$pathPart])) {
                if ($pathPos === $pathLen) {
                    return null;
                }
                array_unshift($pathParts, $pathPart.$path->getSeparator().array_shift($pathParts));
                continue;
            }
            Assertion::isArray($value, sprintf('Trying to traverse non array-value with path: "%s"', $path));
            $value = &$value[$pathPart];
        }
        return $value;
    }

    private function interpolateConfigValues(array $config): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->interpolateConfigValues($value);
            }
            if (is_string($value) && preg_match_all(self::INTERPOLATION_PATTERN, $value, $matches)) {
                return str_replace($matches[0], array_map([$this, "get"], $matches[2]), $value);
            }
            return $value;
        }, $config);
    }
}
