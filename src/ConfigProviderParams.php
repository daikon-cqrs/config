<?php
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

final class ConfigProviderParams implements ConfigProviderParamsInterface
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $this->verifyParams($params);
    }

    public function hasScope(string $scope): bool
    {
        return isset($this->params[$scope]);
    }

    public function getLoader(string $scope): ConfigLoaderInterface
    {
        $this->assertScopeExists($scope);
        $loader = $this->params[$scope]["loader"];
        if (!is_object($loader)) {
            $this->params[$scope]["loader"] = new $loader;
        }
        return $this->params[$scope]["loader"];
    }

    public function getLocations(string $scope): array
    {
        $this->assertScopeExists($scope);
        return $this->params[$scope]["locations"];
    }

    public function getSources(string $scope): array
    {
        $this->assertScopeExists($scope);
        return $this->params[$scope]["sources"];
    }

    private function verifyParams(array $params): array
    {
        if (empty($params)) {
            throw new \Exception("Given params may not be empty.");
        }
        foreach ($params as $scope => $scopeParams) {
            if (isset($scopeParams["locations"])) {
                $this->checkLocations($scope, $scopeParams);
            } else {
                $params[$scope]["locations"] = [];
            }
            $this->checkSources($scope, $scopeParams);
            $this->checkLoader($scope, $scopeParams);
        }
        return $params;
    }

    private function checkLocations(string $scope, array $scopeParams)
    {
        if (!is_array($scopeParams['locations'])) {
            throw new \Exception("The 'locations' param within scope: '$scope' must be an array");
        }
    }

    private function checkSources(string $scope, array $params)
    {
        if (!isset($params['sources'])) {
            throw new \Exception("Missing required key 'sources' within scope: '$scope'");
        }
        if (!is_array($params['sources'])) {
            throw new \Exception("The 'sources' param within scope: '$scope' must be an array");
        }
    }

    private function checkLoader(string $scope, array $params)
    {
        if (!isset($params['loader'])) {
            throw new \Exception("Missing required key 'loader' within scope: '$scope'");
        }
        if (!is_string($params['loader'])) {
            throw new \Exception("The 'loader' param within scope: '$scope' must be a string(fqcn)");
        }
        if (!class_exists($params['loader'])) {
            throw new \Exception(
                "Configured loader: '".$params['loader']."' for scope: '$scope' can not be found."
            );
        }
        $implementedInterfaces = class_implements($params['loader']);
        if (!in_array(ConfigLoaderInterface::class, $implementedInterfaces)) {
            throw new \Exception(
                "Configured loader: '".$params['loader']."' for scope: '$scope' ".
                "does not implement required interface: ".ConfigLoaderInterface::class
            );
        }
    }

    private function assertScopeExists(string $scope)
    {
        if (!$this->hasScope($scope)) {
            throw new \Exception("Given scope: '$scope' has not been registered.");
        }
    }
}
