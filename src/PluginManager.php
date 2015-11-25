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

use Yii;
use ReflectionClass;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;

/**
 * Plugin Manager.
 *
 * Usage, in config:
 * ~~~
 * 'pluginManager' => [
 *     'class' => 'hiqdev\pluginmanager\PluginManager',
 * ],
 * ~~~
 */
class PluginManager extends \hiqdev\yii2\collection\Object implements BootstrapInterface
{
    /**
     * @var int|bool the duration of caching in seconds, default 3600
     *               When false - caching is disabled.
     */
    public $cacheDuration = 3600;

    protected $_app;

    public function getApp()
    {
        return $this->_app;
    }

    /**
     * Adds given plugins. Doesn't delete old.
     */
    public function setPlugins(array $plugins)
    {
        $this->setItem('plugins', array_merge((array) $this->rawItem('plugins'), $plugins));
    }

    /**
     * @var bool is already bootstrapped.
     */
    protected $_isBootstrapped = false;

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        $this->_app = $app;
        if ($this->_isBootstrapped) {
            return;
        }
        if ($this->cache) {
            Yii::trace('Bootstrap from cache', get_called_class() . '::bootstrap');
            $this->setItems($this->cache);
            $this->toArray(); /// XXX strangely doesn't work without it
        } else {
            Yii::trace('Bootstrap plugins from the list of extensions', get_called_class() . '::bootstrap');
            foreach ($app->extensions as $name => $extension) {
                foreach ($extension['alias'] as $alias => $path) {
                    $class = strtr(substr($alias, 1) . '/' . 'Plugin', '/', '\\');
                    if (!class_exists($class)) {
                        continue;
                    }
                    $ref = new ReflectionClass($class);
                    if ($ref->isSubclassOf('hiqdev\pluginmanager\Plugin')) {
                        $plugin = Yii::createObject($class);
                        if ($plugin instanceof BootstrapInterface) {
                            $plugin->bootstrap($app);
                        }
                        $this->setPlugins([$name => $plugin]);
                        // $this->mergeItems($plugin->getItems());
                        foreach ($plugin->getItems() as $k => $v) {
                            $this->_items[$k] = ArrayHelper::merge((array) $this->_items[$k], $v);
                        }
                    }
                }
            }
            $this->saveCache($this->toArray());
        }
        if ($this->aliases) {
            $app->setAliases($this->aliases);
        }
        if ($this->modules) {
            $app->setModules(ArrayHelper::merge($this->modules, $app->modules));
        }
        if ($this->components) {
            $app->setComponents(ArrayHelper::merge($this->components, $app->components));
        }
        /// TODO: get rid of. Line above produces endless loop.
        if ($translations = $this->getItem('translations')) {
            $app->i18n->translations = ArrayHelper::merge($app->i18n->translations, $translations);
        }
        $this->_isBootstrapped = true;
        if ($app->has('menuManager')) {
            $app->menuManager->bootstrap($app);
        }
        if ($app->has('themeManager')) {
            $app->themeManager->bootstrap($app);
        }
    }

    protected $_cache;

    public function getCache()
    {
        if ($this->_cache === null) {
            $this->_cache = $this->loadCache();
        }

        return $this->_cache;
    }

    /**
     * Loads items from the cache. The key is generated automatically using [[buildCacheKey()]].
     *
     * @return array
     *
     * @see buildCacheKey()
     */
    protected function loadCache()
    {
        if ($this->cacheDuration === false) {
            return [];
        }

        return $this->app->cache->get($this->buildCacheKey());
    }

    /**
     * Saves the $value to the cache. The key is generated with [[buildCacheKey()]].
     *
     * @param $value mixed
     *
     * @return bool
     *
     * @see buildCacheKey()
     */
    protected function saveCache($value)
    {
        return $this->app->cache->set($this->buildCacheKey(), $value, $this->cacheDuration);
    }

    /**
     * @return array
     */
    protected function buildCacheKey()
    {
        return [
            'name'       => get_called_class(),
            'app'        => $this->app->className(),
            'lang'       => $this->app->language,
            'extensions' => $this->app->extensions,
        ];
    }
}
