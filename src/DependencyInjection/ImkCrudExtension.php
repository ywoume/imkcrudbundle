<?php

namespace ImkCrudBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ImkCrudExtension extends Extension
{
    /**
     * @var array
     */
    private $config;

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Services')
        );
        $loader->load('crud.xml');

        $configuration = new ImkCrudConfiguration();

        $this->config = $this->processConfiguration($configuration, $configs);

    }
}
