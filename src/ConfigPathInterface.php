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
    public static function fromString(string $path, string $separator): ConfigPathInterface;

    public function getScope(): string;

    public function getParts(): array;

    public function hasParts(): bool;

    public function getLength(): int;

    public function getSeparator(): string;

    public function __toString(): string;
}
