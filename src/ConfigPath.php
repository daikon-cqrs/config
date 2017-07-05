<?php
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

use Assert\Assertion;

final class ConfigPath implements ConfigPathInterface
{
    private const PATH_SEP = '.';

    private $scope;

    private $parts;

    private $separator;

    public static function fromString(string $path, string $separator = self::PATH_SEP): ConfigPathInterface
    {
        Assertion::notSame(
            0,
            strpos($path, $separator),
            'Initializing malformed ConfigPath: Path may not start with: '.$separator
        );
        $pathParts = explode(self::PATH_SEP, $path);
        return new static(array_shift($pathParts), $pathParts, $separator);
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public function hasParts(): bool
    {
        return !empty($this->parts);
    }

    public function getLength(): int
    {
        return count($this->parts);
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function __toString(): string
    {
        $pathParts = $this->parts;
        array_unshift($pathParts, $this->scope);
        return join(self::PATH_SEP, $pathParts);
    }

    private function __construct(string $scope, array $parts, string $separator)
    {
        Assertion::notEmpty($scope, 'Trying to create ConfigPath from empty scope.');
        $this->separator = $separator;
        $this->scope = $scope;
        $this->parts = $parts;
    }
}
