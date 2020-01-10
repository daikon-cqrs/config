<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Test\Config;

use Daikon\Config\ArrayConfigLoader;
use PHPUnit\Framework\TestCase;

final class ArrayConfigLoaderTest extends TestCase
{
    private const ARRAY_FIXTURE = [
        'couchdb' => [
            'host' => '127.0.0.1',
            'port' => 5984,
            'transport' => 'https',
            'user' => 'couchdb',
            'password' => 'couchdb'
        ]
    ];

    public function testLoad()
    {
        $arrayLoader = new ArrayConfigLoader;
        $this->assertEquals(self::ARRAY_FIXTURE, $arrayLoader->load([], self::ARRAY_FIXTURE));
    }
}
