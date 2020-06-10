<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Config;

use Daikon\Interop\Assert;
use Daikon\Interop\Assertion;

final class ConfigProviderParams implements ConfigProviderParamsInterface
{
    private array $params;

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

    private function checkLocations(string $scope, array $params): void
    {
        Assertion::isArray($params['locations'], "The 'locations' param within scope '$scope' must be an array.");
    }

    private function checkSources(string $scope, array $params): void
    {
        Assertion::keyIsset($params, 'sources', "Missing required key 'sources' within scope '$scope'.");
        Assertion::isArray($params['sources'], "The 'sources' param within scope '$scope' must be an array.");
    }

    private function checkLoader(string $scope, array $params): void
    {
        Assertion::keyIsset($params, 'loader', "Missing required key 'loader' within scope '$scope'.");
        if ($params['loader'] instanceof ConfigLoaderInterface) {
            return;
        }
        Assert::that($params['loader'])
            ->string("The 'loader' param within scope '$scope' must be a fqcn string.")
            ->classExists("Configured loader '{$params['loader']}' for scope '$scope' cannot be found.")
            ->implementsInterface(
                ConfigLoaderInterface::class,
                sprintf(
                    "Configured loader '%s' for scope '%s' does not implement required interface '%s'.",
                    $params['loader'],
                    $scope,
                    ConfigLoaderInterface::class
                )
            );
    }

    private function assertScopeExists(string $scope): void
    {
        Assertion::true($this->hasScope($scope), "Given scope '$scope' has not been registered.");
    }
}
