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

use hiqdev\php\collection\ArrayHelper;
use ReflectionClass;
use Yii;
use yii\base\BootstrapInterface;

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
        if ($this->_isBootstrapped) {
            return;
        }
        $this->_isBootstrapped = true;
        $this->_app = $app;
        if ($this->cache) {
            Yii::trace('Bootstrap from cache', get_called_class() . '::bootstrap');
            $this->setItems($this->cache);
            // $this->toArray(); // TODO 2 SilverFire: check and remove this line
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
                        //$this->setPlugins([$name => $plugin]);
                        $this->mergeItems($plugin->getItems());
                    }
                }
            }
            $this->saveCache($this->getItems());
        }
        if ($this->aliases) {
            $app->setAliases($this->aliases);
        }
        if ($this->modules) {
            $modules = ArrayHelper::getItems($app->modules, array_keys($this->modules));
            $this->modules = ArrayHelper::merge($this->modules, $modules);
            $app->setModules($this->modules);
        }
        if ($this->components) {
            $components = ArrayHelper::getItems($app->components, array_keys($this->components));
            $this->components = ArrayHelper::merge($this->components, $components);
            $app->setComponents($this->components);
        }
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

        return $this->serializer->unserialize($this->app->cache->get($this->buildCacheKey()));
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
        return $this->app->cache->set($this->buildCacheKey(), $this->serializer->serialize($value), $this->cacheDuration);
    }

    protected $_serializer;

    public function getSerializer()
    {
        if ($this->_serializer === null) {
            $this->_serializer = new Serializer();
        }

        return $this->_serializer;
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
