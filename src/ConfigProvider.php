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

    /** @var mixed[] */
    private $config;

    /** @var ConfigProviderParamsInterface */
    private $params;

    /** @var mixed[] */
    private $paramInterpolations;

    public function __construct(ConfigProviderParamsInterface $params)
    {
        $this->params = $params;
        $this->config = [];
        $this->paramInterpolations = [];
    }

    public function get(string $path, $default = null)
    {
        $path = ConfigPath::fromString($path);
        $scope = $path->getScope();
        Assertion::keyNotExists(
            $this->paramInterpolations,
            $scope,
            'Recursive interpolations are not allowed when interpolating "locations" or "sources". '.
            sprintf('Trying to recurse into scope: "%s"', $scope)
        );
        if (!isset($this->config[$scope]) && $this->params->hasScope($scope)) {
            $this->config[$scope] = $this->loadScope($scope);
        } elseif (!isset($this->config[$scope])) {
            return $default;
        }
        return $this->resolvePath($path) ?? $default;
    }

    public function has(string $path): bool
    {
        return $this->get($path) !== null;
    }

    private function loadScope(string $scope)
    {
        $this->paramInterpolations[$scope] = true;
        $locations = $this->params->getLocations($scope);
        $sources = $this->params->getSources($scope);
        $loader = $this->params->getLoader($scope);
        if (!$loader instanceof ArrayConfigLoader) {
            $sources = $this->interpolateConfigValues($sources);
        }
        $locations = $this->interpolateConfigValues($locations);
        unset($this->paramInterpolations[$scope]);

        $this->config[$scope] = $loader->load($locations, $sources);
        return $this->interpolateConfigValues($this->config[$scope]);
    }

    private function resolvePath(ConfigPathInterface $path)
    {
        $pos = 0;
        $length = $path->getLength();
        $parts = $path->getParts();
        $value = &$this->config[$path->getScope()];
        while (!empty($parts)) {
            $pos++;
            $part = array_shift($parts);
            if (!isset($value[$part])) {
                if ($pos === $length) {
                    return null;
                }
                array_unshift($parts, $part.$path->getSeparator().array_shift($parts));
                continue;
            }
            Assertion::isArray($value, sprintf('Trying to traverse non array-value with path: "%s"', $path));
            $value = &$value[$part];
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
