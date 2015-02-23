<?php

namespace Graviton\RestBundle\Listener;


use Symfony\Component\HttpFoundation\Response;

class XVersionResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider versionProvider
     */
    public function testOnKernelResponse($version, $file = '')
    {
        $response = new Response();

        $eventDouble = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getResponse'))
            ->getMock();
        $eventDouble
            ->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $loggerDouble = $this->getMockBuilder('\Psr\Log\LoggerInterface')
            ->setMethods(array('warning'))
            ->getMockForAbstractClass();
        $loggerDouble
            ->expects($this->any())
            ->method('warning')
            ->with($this->contains('Unable to extract version from composer.json file'));

        $listener = new XVersionResponseListener($loggerDouble, $file);
        $listener->onKernelResponse($eventDouble);

        $this->assertEquals($version, $response->headers->get('X-VERSION'));
    }

    public function versionProvider()
    {
        $version = json_decode(file_get_contents(__DIR__ . '/../../../../../composer.json'), true);
        $version = $version['version'];

        return array(
            'composer file not found' => array(XVersionResponseListener::X_VERSION_DEFAULT, 'invalidPath'),
            'composer file found'     => array($version, __DIR__ . '/../../../../../composer.json'),
            'composer file not set'   => array($version),
        );
    }
}
