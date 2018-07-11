<?php

namespace App\CacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class AppCacheExtension.
 *
 * @package App\CacheBundle\DependencyInjection
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class AppCacheExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('managers.yml');
        $loader->load('subscribers.yml');
    }
}
