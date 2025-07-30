<?php

namespace Charcoal\FilePond;

// from 'charcoal-app'
use Charcoal\App\App;
use Charcoal\App\Module\AbstractModule;

// from 'charcoal-contrib-filepond'
use Charcoal\FilePond\Action\RequestAction;
use Charcoal\FilePond\ServiceProvider\FilePondServiceProvider;

// from 'pimple'
use DI\Container;

// from 'Psr-7'
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;
use Slim\Interfaces\RouteCollectorProxyInterface;

/**
 * FilePond Module
 */
class FilePondModule extends AbstractModule
{
    /**
     * Setup the module's dependencies.
     *
     * @return AbstractModule
     * @throws RuntimeException When the server_end_point is not a string.
     */
    public function setup()
    {
        /** @var Container $container */
        $container = $this->app()->getContainer();

        (new FilePondServiceProvider())->register($container);

        $servers = $container->get('file-pond/config')->getServers();

        foreach ($servers as $serverIdent => $server) {
            $route = $server['route'];

            if (!is_string($route)) {
                throw new RuntimeException(sprintf(
                    'Invalid type for \'route\'. Expected \'string\', but got [%s] in [%s]',
                    gettype($route),
                    get_class($this)
                ));
            }

            $route = '/'.ltrim($route, '\/ ');

            $this->app()->group(rtrim($route, '/'), function (RouteCollectorProxyInterface $group) use ($serverIdent) {
                $group->get('', function (Request $request, Response $response) use ($serverIdent) {
                    /** @var Container $container */
                    $container = $this;
                    $action    = new RequestAction([
                        'logger'          => $container->get('logger'),
                        'filePondService' => $container->get('file-pond/service')($serverIdent),
                    ]);

                    return $action($request, $response);
                });

                $group->post('', function (Request $request, Response $response) use ($serverIdent) {
                    /** @var Container $container */
                    $container = $this;
                    $action    = new RequestAction([
                        'logger'          => $container->get('logger'),
                        'filePondService' => $container->get('file-pond/service')($serverIdent),
                    ]);

                    return $action($request, $response);
                });

                $group->delete('', function (Request $request, Response $response) use ($serverIdent) {
                    /** @var Container $container */
                    $container = $this;
                    $action    = new RequestAction([
                        'logger'          => $container->get('logger'),
                        'filePondService' => $container->get('file-pond/service')($serverIdent),
                    ]);

                    return $action($request, $response);
                });
            });
        }

        return $this;
    }
}
