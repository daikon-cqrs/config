<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Config;

interface ConfigPathInterface
{
    public const WILDCARD_TOKEN = '*';

    public static function fromString(string $path, string $separator): self;

    public function getScope(): string;

    public function getParts(): array;

    public function hasParts(): bool;

    public function getLength(): int;

    public function getSeparator(): string;

    public function __toString(): string;
}
