<?php
/**
 * test loader and loader strategies
 */

namespace Graviton\GeneratorBundle\Tests\Definition\Strategy;

use Graviton\GeneratorBundle\Definition\Loader\Strategy\ScanStrategy;
use Graviton\GeneratorBundle\Definition\JsonDefinition;

/**
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class ScanStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadDir()
    {
        $jsonDef = array(
            new JsonDefinition(__DIR__.'/resources/definition/test.json'),
        );

        $sut = new ScanStrategy;
        $sut->setScanDir(__DIR__);
        $this->assertTrue($sut->accepts(null));
        $this->assertEquals($jsonDef, $sut->load(null));
    }
}
