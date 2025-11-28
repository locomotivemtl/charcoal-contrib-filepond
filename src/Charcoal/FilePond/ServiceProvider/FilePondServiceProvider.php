<?php

namespace Charcoal\FilePond\ServiceProvider;

use Charcoal\FilePond\FilePondConfig;
use Charcoal\FilePond\Service\FilePondService;
use Psr\Container\ContainerInterface;
use DI\Container;

/**
 * File Pond Service Provider
 */
class FilePondServiceProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $container A container instance.
     * @return void
     */
    public function register(ContainerInterface $container)
    {
        /**
         * @param Container $container The PHP-DI container.
         * @return FilePondConfig
         */
        $container->set('file-pond/config', function (ContainerInterface $container) {
            $config = $container->get('config')->get('contrib.file-pond.config') ?: [];
            return new FilePondConfig($config);
        });

        /**
         * @param Container $container The PHP-DI container.
         * @return FilePondService
         */
        $container->set('file-pond/service', function (ContainerInterface $container) {
            return new FilePondService(
                $container->get('file-pond/config'),
                $container->get('filesystem/config'),
                $container->get('filesystem/manager'),
                $container->get('filesystems')
            );
        });
    }
}
