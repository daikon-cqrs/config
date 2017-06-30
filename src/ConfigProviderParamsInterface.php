<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

interface ConfigProviderParamsInterface
{
    public function hasScope(string $scope): bool;

    public function getLoader(string $scope): ConfigLoaderInterface;

    public function getLocations(string $scope): array;

    public function getSources(string $scope): array;
}
