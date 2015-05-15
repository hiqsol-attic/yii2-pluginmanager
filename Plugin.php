<?php
/**
 * @link    http://hiqdev.com/yii2-pluginmanager
 * @license http://hiqdev.com/yii2-pluginmanager/license
 * @copyright Copyright (c) 2015 HiQDev
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
 *             [
 *                 'class' => 'hipanel\modules\client\SidebarMenu',
 *             ],
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
class Plugin extends \hiqdev\collection\Object
{
}
