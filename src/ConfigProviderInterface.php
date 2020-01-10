<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Config;

interface ConfigProviderInterface
{
    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $path, $default = null);

    public function has(string $path): bool;

    /**
     * @param mixed $default
     * @return mixed
     */
    public function __invoke(string $path, $default = null);
}
