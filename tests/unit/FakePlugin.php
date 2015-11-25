<?php

/*
 * Hipanel Module Dns
 *
 * @link      https://github.com/hiqdev/hipanel-module-dns
 * @package   hipanel-module-dns
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\pluginmanager\tests\unit;

class FakePlugin extends \hiqdev\pluginmanager\Plugin
{
    protected $_items = [
        'aliases' => [
            '@dns' => '/dns/',
        ],
        'modules' => [
            'dns' => 'dns config here',
        ],
        'components' => [
            'i18n' => 'i18n config here',
        ],
    ];

    public function items()
    {
        return [
            'aliases' => [
                '@more' => '/more',
            ],
        ];
    }
}
