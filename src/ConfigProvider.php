<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

final class ConfigProvider implements ConfigProviderInterface
{
    private const INTERPOLATION_PATTERN = '/(\$\{(.*?)\})/';

    private $config;

    private $configParams;

    public function __construct(ConfigProviderParamsInterface $configParams)
    {
        $this->configParams = $configParams;
    }

    public function get(string $path, $default = null)
    {
        $configPath = $this->buildPath($path);
        $scopeConfig = $this->retrieveScope($configPath);
        if ($configPath->hasWildcardNamespace()) {
            return $scopeConfig;
        }
        if (isset($scopeConfig[$configPath->getNamespace()]) && $configPath->hasWildcardKey()) {
            return $scopeConfig[$configPath->getNamespace()];
        }
        return $this->findNamespaceValue($scopeConfig[$configPath->getNamespace()], $configPath) ?? $default;
    }

    public function has(string $path): bool
    {
        try {
            return $this->get($path) !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function retrieveScope(ConfigPathInterface $path): array
    {
        $scope = $path->getScope();
        if (isset($this->config[$scope])) {
            return $this->config[$scope];
        }
        $this->config[$scope] = $this->interpolateConfigValues(
            $this->configParams->getLoader($path)->load(
                $this->configParams->getLocations($path),
                $this->configParams->getSources($path)
            )
        );
        return $this->config[$scope];
    }

    private function buildPath(string $path): ConfigPathInterface
    {
        return ConfigPath::fromPathString(
            $path,
            $this->configParams->getDefaultScope(),
            $this->configParams->getDefaultNamespace()
        );
    }

    private function findNamespaceValue(array $namespace, ConfigPathInterface $path)
    {
        $value = &$namespace;
        $keyParts = explode(".", $path->getKey());
        do {
            $curKey = array_shift($keyParts);
            if (!is_array($value)) {
                throw new \Exception("Trying to traverse non array-value with key: '".$path->getKey()."'");
            }
            if (!isset($value[$curKey])) {
                return null;
            }
            $value = &$value[$curKey];
        } while (!empty($keyParts));
        return $value;
    }

    private function interpolateConfigValues(array $config): array
    {
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $config[$key] = $this->interpolateConfigValues($value);
            } elseif (is_string($value) && preg_match_all(self::INTERPOLATION_PATTERN, $value, $matches)) {
                $replacements = [];
                foreach ($matches[2] as $configKey) {
                    $replacements[] = $this->get($configKey);
                }
                $config[$key] = str_replace($matches[0], $replacements, $value);
            }
        }
        return $config;
    }
}
