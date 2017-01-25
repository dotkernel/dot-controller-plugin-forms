<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller-plugin-forms
 * @author: n3vra
 * Date: 1/25/2017
 * Time: 2:37 PM
 */

namespace Dot\Controller\Plugin\Forms\Factory;

use Dot\Controller\Plugin\Forms\FormsPlugin;
use Interop\Container\ContainerInterface;

/**
 * Class FormsPluginFactory
 * @package Dot\Controller\Plugin\Forms\Factory
 */
class FormsPluginFactory
{
    /**
     * @param ContainerInterface $container
     * @return FormsPlugin
     */
    public function __invoke(ContainerInterface $container)
    {
        return new FormsPlugin(
            $container->get('FormElementManager'),
            $container
        );
    }
}
