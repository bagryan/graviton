<?php
/**
 * manage and load bundle config.
 */

namespace Graviton\DocumentBundle\DependencyInjection;

use Graviton\BundleBundle\DependencyInjection\GravitonBundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://scm.to/004w}
 *
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class GravitonDocumentExtension extends GravitonBundleExtension
{
    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getConfigDir()
    {
        return __DIR__ . '/../Resources/config';
    }

    /**
     * {@inheritDoc}
     *
     * Overwrite mongodb config from parent in cloud case.
     *
     * @param ContainerBuilder $container instance
     *
     * @return void
     */
    public function prepend(ContainerBuilder $container)
    {
        parent::prepend($container);

        /** [nue]
         * this is a workaround for a current symfony bug:
         * https://github.com/symfony/symfony/issues/7555
         *
         * we *want* to be able to override any param with our env variables..
         * so we do again, what the kernel did already here.. ;-)
         *
         * @todo move this out of file bundle as it is much more global
         * @todo add proper documentation on this "feature" to a README
         */
        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'SYMFONY__')) {
                $container->setParameter(strtolower(str_replace('__', '.', substr($key, 9))), $value);
            }
        }

        // populated from cf's VCAP_SERVICES variable
        $services = $container->getParameter('vcap.services');
        if (!empty($services)) {
            $services = json_decode($services, true);
            $mongo = $services['mongodb-2.2'][0]['credentials'];

            $container->setParameter('mongodb.default.server.uri', $mongo['url']);
            $container->setParameter('mongodb.default.server.db', $mongo['db']);
        } else {
            $container->setParameter(
                'mongodb.default.server.uri',
                $container->getParameter('graviton.mongodb.default.server.uri')
            );
            $container->setParameter(
                'mongodb.default.server.db',
                $container->getParameter('graviton.mongodb.default.server.db')
            );
        }
    }
}
