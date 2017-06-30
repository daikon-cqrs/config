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
    public function getDefaultScope(): string;

    public function getDefaultNamespace(): string;

    public function getLoader(ConfigPathInterface $configPath): ConfigLoaderInterface;

    public function getLocations(ConfigPathInterface $configPath): array;

    public function getSources(ConfigPathInterface $configPath): array;
}
