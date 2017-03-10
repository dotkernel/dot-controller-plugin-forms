<?php
/**
 * @see https://github.com/dotkernel/dot-controller-plugin-forms/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller-plugin-forms/blob/master/LICENSE.md MIT License
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
