<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Config;

use Daikon\Interop\Assertion;

final class ConfigProvider implements ConfigProviderInterface
{
    private const INTERPOLATION_PATTERN = '/(\$\{(.*?)\})/';

    private array $config;

    private ConfigProviderParamsInterface $params;

    private array $paramInterpolations;

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
            "Recursive interpolations are not allowed when interpolating 'locations' or 'sources'. ".
            "Trying to recurse into scope '$scope'"
        );

        if (!isset($this->config[$scope]) && $this->params->hasScope($scope)) {
            $this->config[$scope] = $this->loadScope($scope);
        } elseif (!isset($this->config[$scope])) {
            return $default;
        }

        return $this->evaluatePath(
            $path->getParts(),
            $this->config[$path->getScope()],
            $path->getSeparator()
        ) ?? $default;
    }

    public function has(string $path): bool
    {
        return $this->get($path) !== null;
    }


    public function __invoke(string $path, $default = null)
    {
        $value = $this->get($path, $default);
        Assertion::allNotNull([$value, $default], "Missing required config value at path '$path'");
        return $value;
    }

    private function loadScope(string $scope): array
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

    /** @return mixed */
    private function evaluatePath(array $parts, array $values, string $separator)
    {
        if (empty($values)) {
            return null;
        }
        $pos = 0;
        $length = count($parts);
        $value = &$values;
        while (!empty($parts)) {
            $pos++;
            $part = array_shift($parts);
            Assertion::isArray(
                $value,
                sprintf("Trying to traverse non-array value with path part '%s'", join($separator, $parts))
            );
            if ($part === ConfigPathInterface::WILDCARD_TOKEN) {
                return $this->expandWildcard($parts, $value, $separator);
            } elseif (!isset($value[$part]) && $pos === $length) {
                return null;
            } elseif (!isset($value[$part])) {
                array_unshift($parts, $part.$separator.array_shift($parts));
                continue;
            }
            $value = &$value[$part];
        }
        return $value;
    }

    private function expandWildcard(array $parts, array $context, string $separator): array
    {
        return array_merge(...array_reduce(
            $context,
            function (array $collected, array $ctx) use ($parts, $separator): array {
                $expandedValue = $this->evaluatePath($parts, $ctx, $separator);
                if (!is_null($expandedValue)) {
                    $collected[] =  (array)$expandedValue;
                }
                return $collected;
            },
            []
        ));
    }

    private function interpolateConfigValues(array $config): array
    {
        return array_map([$this, 'mapInterpolation'], $config);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function mapInterpolation($value)
    {
        if (is_array($value)) {
            return $this->interpolateConfigValues($value);
        } elseif (is_string($value) && preg_match_all(self::INTERPOLATION_PATTERN, $value, $matches)) {
            return $this->interpolateConfigValue($value, $matches[0], $matches[2]);
        }
        return $value;
    }

    /** @return mixed */
    private function interpolateConfigValue(string $value, array $valueParts, array $interpolations)
    {
        $interpolatedValues = array_map([$this, 'get'], $interpolations);
        return array_filter($interpolatedValues, 'is_string') === $interpolatedValues
            ? str_replace($valueParts, $interpolatedValues, $value)
            : $interpolatedValues[0];
    }
}
