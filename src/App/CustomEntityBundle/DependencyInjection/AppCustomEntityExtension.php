<?php

namespace App\CustomEntityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class AppCustomEntityExtension.
 *
 * @package App\CustomEntityBundle\DependencyInjection
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class AppCustomEntityExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('managers.yml');
        $loader->load('normalizers.yml');
        $loader->load('subscribers.yml');
    }
}
