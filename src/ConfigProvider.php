<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller-plugin-forms
 * @author: n3vra
 * Date: 1/25/2017
 * Time: 2:27 PM
 */

declare(strict_types = 1);

namespace Dot\Controller\Plugin\Forms;

use Dot\Controller\Plugin\Forms\Factory\FormsPluginFactory;

/**
 * Class ConfigProvider
 * @package Dot\Controller\Factory\Forms
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dot_controller' => [
                'plugin_manager' => [
                    'factories' => [
                        'forms' => FormsPluginFactory::class,
                    ],
                ],
            ],
        ];
    }
}
