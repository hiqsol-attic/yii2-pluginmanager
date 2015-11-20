<?php

/*
 * Plugin Manager for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-pluginmanager
 * @package   yii2-pluginmanager
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\pluginmanager;

/**
 * Plugin Class.
 * Collection of items that plugin brings.
 *
 * Example:
 * ~~~
 * class Plugin extends \hiqdev\pluginmanager\Plugin
 * {
 *     protected $_items = [
 *         'menus' => [
 *             'hipanel\modules\client\SidebarMenu',
 *         ],
 *         'modules' => [
 *             'client' => [
 *                 'class' => 'hipanel\modules\client\Module',
 *             ],
 *         ],
 *     ];
 * }
 * ~~~
 */
class Plugin extends \hiqdev\yii2\collection\Object
{
    /**
     * Default items.
     *
     * @return array
     */
    public function items()
    {
        return [];
    }

    /**
     * Inits with default items.
     */
    public function init()
    {
        parent::init();
        $this->addItems($this->items());
    }
}
