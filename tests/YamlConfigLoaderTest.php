<?php
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Test\Config;

use Daikon\Config\YamlConfigLoader;
use PHPUnit\Framework\TestCase;

final class YamlConfigLoaderTest extends TestCase
{
    public function testLoad()
    {
        $yamlLoader = new YamlConfigLoader;
        $locations = [ __DIR__.'/Fixture/YamlConfigLoader' ];
        $sources = [ 'fixture.yml' ];
        $expecation = require __DIR__.'/Fixture/YamlConfigLoader/load_expectation.php';
        $this->assertEquals($expecation, $yamlLoader->load($locations, $sources));
    }

    public function testCascadedLoad()
    {
        $yamlLoader = new YamlConfigLoader;
        $locations = [ __DIR__.'/Fixture/YamlConfigLoader' ];
        $sources = [ 'fixture*.yml' ];
        $expecation = require __DIR__.'/Fixture/YamlConfigLoader/load_cascaded_expectation.php';
        $this->assertEquals($expecation, $yamlLoader->load($locations, $sources));
    }
}
