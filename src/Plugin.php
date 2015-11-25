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
 * Collection of items that plugin brings: aliases, modules, components.
 *
 * Example:
 * ~~~
 * class Plugin extends \hiqdev\pluginmanager\Plugin
 * {
 *     protected $_items = [
 *         'aliases' => [
 *             '@my/alias' => 'my/path',
 *         ],
 *         'modules' => [
 *             'client' => [
 *                 'class' => 'hipanel\modules\client\Module',
 *             ],
 *         ],
 *         'components' => [
 *             'i18n' => [
 *                 'translations' => [
 *                     'test' => [
 *                         'class'    => 'yii\i18n\PhpMessageSource',
 *                         'basePath' => '@my/alias/messages',
 *                         'fileMap'  => [
 *                             'test' => 'test.php',
 *                         ],
 *                     ],
 *                 ],
 *             ],
 *         ],
 *     ];
 * }
 * ~~~
 */
class Plugin extends \hiqdev\yii2\collection\Object
{
    /**
     * Inits with default items if any.
     */
    public function init()
    {
        parent::init();
        if (method_exists($this, 'items')) {
            $this->mergeItems($this->items());
        }
    }
}
