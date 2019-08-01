<?php
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

interface ConfigProviderInterface
{
    /** @return mixed */
    public function get(string $path, $default = null);

    public function has(string $path): bool;

    public function __invoke(string $path, $default = null);
}
