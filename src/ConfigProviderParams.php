<?php

namespace Daikon\Config;

final class ConfigProviderParams implements ConfigProviderParamsInterface
{
    /**
     * @var mixed[]
     */
    private $params;

    /**
     * @var string
     */
    private $defaultScope;

    /**
     * @var string
     */
    private $defaultNamespace;

    /**
     * @param string $defaultScopeAndNamespace
     * @param mixed[] $params
     */
    public function __construct(array $params, string $defaultScopeAndNamespace)
    {
        foreach ($params as $scope => $scopeParams) {
            $this->verifyParams($scope, $scopeParams);
        }
        $this->params = $params;
        $scopeParts = explode("::", $defaultScopeAndNamespace);
        if (count($scopeParts) !== 2) {
            throw new \Exception("Invalid defaultScopeAndNamespace given.");
        }
        list($this->defaultScope, $this->defaultNamespace) = $scopeParts;
    }

    /**
     * @return string
     */
    public function getDefaultScope(): string
    {
        return $this->defaultScope;
    }

    /**
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        return $this->defaultNamespace;
    }

    /**
     * @param ConfigPathInterface $configPath
     * @return ConfigLoaderInterface
     */
    public function getLoader(ConfigPathInterface $configPath): ConfigLoaderInterface
    {
        $scope = $configPath->getScope();
        $this->assertScopeExists($configPath);
        $loader = $this->params[$scope]["loader"];
        if (!is_object($loader)) {
            $this->params[$scope]["loader"] = new $loader;
        }
        return $this->params[$scope]["loader"];
    }

    /**
     * @param ConfigPathInterface $configPath
     * @return string[]
     */
    public function getLocations(ConfigPathInterface $configPath): array
    {
        $scope = $configPath->getScope();
        $this->assertScopeExists($configPath);
        return $this->params[$scope]["locations"];
    }

    /**
     * @param ConfigPathInterface $configPath
     * @return string[]
     */
    public function getSources(ConfigPathInterface $configPath): array
    {
        $scope = $configPath->getScope();
        $this->assertScopeExists($configPath);
        return $this->params[$scope]["sources"];
    }

    /**
     * @param string $scope
     * @param mixed[] $scopeParams
     */
    private function verifyParams(string $scope, array $scopeParams)
    {
        $this->verifyLocations($scope, $scopeParams);
        $this->verifySources($scope, $scopeParams);
        $this->verifyLoader($scope, $scopeParams);
    }

    /**
     * @param string $scope
     * @param mixed[] $scopeParams
     */
    private function verifyLocations(string $scope, array $scopeParams)
    {
        if (!isset($scopeParams['locations'])) {
            throw new \Exception("Missing required key 'locations' within scope: '$scope'");
        }
        if (!is_array($scopeParams['locations'])) {
            throw new \Exception("The 'locations' param within scope: '$scope' must be an array");
        }
    }

    /**
     * @param string $scope
     * @param mixed[] $scopeParams
     */
    private function verifySources(string $scope, array $scopeParams)
    {
        if (!isset($scopeParams['sources'])) {
            throw new \Exception("Missing required key 'sources' within scope: '$scope'");
        }
        if (!is_array($scopeParams['sources'])) {
            throw new \Exception("The 'sources' param within scope: '$scope' must be an array");
        }
    }

    /**
     * @param string $scope
     * @param mixed[] $scopeParams
     */
    private function verifyLoader(string $scope, array $scopeParams)
    {
        if (!isset($scopeParams['loader'])) {
            throw new \Exception("Missing required key 'loader' within scope: '$scope'");
        }
        if (!is_string($scopeParams['loader'])) {
            throw new \Exception("The 'loader' param within scope: '$scope' must be a string(fqcn)");
        }
        if (!class_exists($scopeParams['loader'])) {
            throw new \Exception(
                "Configured loader: '".$scopeParams['loader']."' for scope: '$scope' can not be found."
            );
        }
        $implementedInterfaces = class_implements($scopeParams['loader']);
        if (!in_array(ConfigLoaderInterface::class, $implementedInterfaces)) {
            throw new \Exception(
                "Configured loader: '".$scopeParams['loader']."' for scope: '$scope' ".
                "does not implement required interface: ".ConfigLoaderInterface::class
            );
        }
    }

    /**
     * @param ConfigPathInterface $configPath
     */
    private function assertScopeExists(ConfigPathInterface $configPath)
    {
        $scope = $configPath->getScope();
        if (!isset($this->params[$scope])) {
            throw new \Exception("$configPath: Given scope: '$scope' has not been registered.");
        }
    }
}
