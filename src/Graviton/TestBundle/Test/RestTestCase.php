<?php

namespace Graviton\TestBundle\Test;

use Symfony\Component\HttpFoundation\Response;

/**
 * REST test case
 *
 * Contains additional helpers for testing RESTful servers
 *
 * @todo refactor alot (use overridden client and whatnot)
 *
 * @category GravitonTestBundle
 * @package  Graviton
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class RestTestCase extends GravitonTestCase
{
    /**
     * Create a REST Client.
     *
     * Creates a regular client first so we can profit from the bootstrapping code
     * in parent::createClient and is otherwise API compatible with said method.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server  An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createRestClient(array $options = array(), array $server = array())
    {
        parent::createClient($options, $server);

        $client = static::$kernel->getContainer()->get('graviton.test.rest.client');
        $client->setServerParameters($server);

        return $client;
    }

    /**
     * test for content type based on classname based mapping
     *
     * @param String   $contentType Expected Content-Type
     * @param Response $response    Response from client
     *
     * @return void
     */
    public function assertResponseContentType($contentType, Response $response)
    {
        $this->assertEquals(
            $contentType,
            $response->headers->get('Content-Type'),
            'Content-Type mismatch in response'
        );
    }
}