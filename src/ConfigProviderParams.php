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
        $loader = $this->params[$scope]['loader'];
        if (!is_object($loader)) {
            $this->params[$scope]['loader'] = new $loader;
        }
        return $this->params[$scope]['loader'];
    }

    public function getLocations(string $scope): array
    {
        $this->assertScopeExists($scope);
        return $this->params[$scope]['locations'];
    }

    public function getSources(string $scope): array
    {
        $this->assertScopeExists($scope);
        return $this->params[$scope]['sources'];
    }

    private function verifyParams(array $params): array
    {
        Assertion::notEmpty($params, 'Given params may not be empty.');
        foreach ($params as $scope => $scopeParams) {
            if (isset($scopeParams['locations'])) {
                $this->checkLocations($scope, $scopeParams);
            } else {
                $params[$scope]['locations'] = [];
            }
            $this->checkSources($scope, $scopeParams);
            $this->checkLoader($scope, $scopeParams);
        }
        return $params;
    }

    private function checkLocations(string $scope, array $scopeParams)
    {
        Assertion::isArray(
            $scopeParams['locations'],
            sprintf('The "locations" param within scope: "%s" must be an array', $scope)
        );
    }

    private function checkSources(string $scope, array $params)
    {
        Assertion::keyIsset($params, 'sources', sprintf('Missing required key "sources" within scope: "%s"', $scope));
        Assertion::isArray(
            $params['sources'],
            sprintf('The "sources" param within scope: "%s" must be an array', $scope)
        );
    }

    private function checkLoader(string $scope, array $params)
    {
        Assertion::keyIsset($params, 'loader', sprintf('Missing required key "loader" within scope: "%s"', $scope));
        if ($params['loader'] instanceof ConfigLoaderInterface) {
            return;
        }
        Assertion::string(
            $params['loader'],
            sprintf('The "loader" param within scope: "%s" must be a string(fqcn)', $scope)
        );
        Assertion::classExists(
            $params['loader'],
            sprintf('Configured loader: "%s" for scope: "%s" can not be found.', $params['loader'], $scope)
        );
        Assertion::implementsInterface(
            $params['loader'],
            ConfigLoaderInterface::class,
            sprintf(
                'Configured loader: "%s" for scope: "%s" does not implement required interface: %s',
                $params['loader'],
                $scope,
                ConfigLoaderInterface::class
            )
        );
    }

    private function assertScopeExists(string $scope)
    {
        Assertion::true($this->hasScope($scope), sprintf('Given scope: "%s" has not been registered.', $scope));
    }
}
