<?php

namespace PiedWeb\StaticBundle\Tests;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;
use PiedWeb\CMSBundle\Tests\AppKernel as CMSAppKernel;

class AppKernel extends CMSAppKernel
{

    public function registerBundles()
    {
        $get = parent::registerBundles();
        $get[]= new \PiedWeb\StaticBundle\PiedWebStatucBundle();
        return $get;
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes = parent::configureRoutes($routes);
        $routes->import(__DIR__.'/../Resources/config/routes/static.yaml');
    }
}
