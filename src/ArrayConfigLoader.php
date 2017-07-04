<?php
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

use Symfony\Component\Yaml\Yaml;

final class ArrayConfigLoader implements ConfigLoaderInterface
{
    public function load(array $locations, array $sources): array
    {
        return $sources;
    }

    public function serialize(array $config): string
    {
        // not implemented yet
        return '';
    }

    public function deserialize(string $serializedConfig): array
    {
        // not implemented yet
        return [];
    }
}
