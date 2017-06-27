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
            $this->verifyScopeParams($scope, $scopeParams);
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
     * @return mixed[]
     */
    public function getNamespaces(ConfigPathInterface $configPath): array
    {
        $scope = $configPath->getScope();
        $this->assertScopeExists($configPath);
        return $this->params[$scope]["namespaces"];
    }

    /**
     * @param ConfigPathInterface $configPath
     * @return mixed[]
     */
    public function getNamespace(ConfigPathInterface $configPath): array
    {
        $scope = $configPath->getScope();
        $namespace = $configPath->getNamespace();
        $this->assertScopeExists($configPath);
        if (!isset($this->params[$scope][$namespace])) {
            throw new \Exception(
                "$configPath: Given namespace: '$namespace', is unknown within scope: '$scope'"
            );
        }
        return $this->params[$scope][$namespace];
    }

    /**
     * @param ConfigPathInterface $configPath
     * @return string[]
     */
    public function getLookupPatterns(ConfigPathInterface $configPath): array
    {
        $scope = $configPath->getScope();
        $this->assertScopeExists($configPath);
        return $this->params[$scope]["lookup_patterns"];
    }

    /**
     * @param string $scope
     * @param mixed[] $scopeParams
     */
    private function verifyScopeParams(string $scope, array $scopeParams)
    {
        $this->verifyScopeNamespaces($scope, $scopeParams);
        $this->verifyScopeLookupPatterns($scope, $scopeParams);
        $this->verifyScopeLoader($scope, $scopeParams);
    }

    /**
     * @param string $scope
     * @param mixed[] $scopeParams
     */
    private function verifyScopeNamespaces(string $scope, array $scopeParams)
    {
        if (!isset($scopeParams['namespaces'])) {
            throw new \Exception("Missing required key 'namespaces' within scope: '$scope'");
        }
        if (!is_array($scopeParams['namespaces'])) {
            throw new \Exception("The 'namespaces' param within scope: '$scope' must be an array");
        }
        foreach ($scopeParams['namespaces'] as $namespace => $namespaceParams) {
            if (is_numeric($namespace)) {
                throw new \Exception(
                    "Namespace identifiers may not be numeric, ".
                    "but given namespace: '$namespace' within scope: '$scope' is numeric."
                );
            }
            if (!is_array($namespaceParams)) {
                throw new \Exception(
                    "Namespace locations must be specified within an array. ".
                    "Non-array value for namespace: '$namespace' given within scope: '$scope'"
                );
            }
        }
    }

    /**
     * @param string $scope
     * @param mixed[] $scopeParams
     */
    private function verifyScopeLookupPatterns(string $scope, array $scopeParams)
    {
        if (!isset($scopeParams['lookup_patterns'])) {
            throw new \Exception("Missing required key 'lookup_patterns' within scope: '$scope'");
        }
        if (!is_array($scopeParams['lookup_patterns'])) {
            throw new \Exception("The 'lookup_patterns' param within scope: '$scope' must be an array");
        }
    }

    /**
     * @param string $scope
     * @param mixed[] $scopeParams
     */
    private function verifyScopeLoader(string $scope, array $scopeParams)
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
