<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

interface ConfigPathInterface
{

    public static function fromPathString(
        string $pathString,
        string $defaultScope,
        string $defaultNamespace
    ): ConfigPathInterface;


    public function getScope(): string;


    public function getNamespace(): string;


    public function hasWildcardNamespace(): bool;


    public function getKey(): string;


    public function hasWildcardKey(): bool;


    public function __toString(): string;
}
