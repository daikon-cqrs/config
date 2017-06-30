<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

interface ConfigLoaderInterface
{
    public function load(array $locations, array $sources): array;

    public function serialize(array $config): string;

    public function deserialize(string $serializedConfig): array;
}
