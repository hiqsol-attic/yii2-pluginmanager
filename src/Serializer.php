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

use Closure;
use SuperClosure\Serializer as SCS;

class Serializer
{
    public function serialize($data)
    {
        return serialize($this->pack($data));
    }

    public function pack($data)
    {
        if ($data instanceof Closure) {
            return new Holder($data);
        } elseif (is_array($data)) {
            foreach ($data as $k => &$v) {
                $v = $this->pack($v);
            }
        }

        return $data;
    }

    public function unserialize($data)
    {
        return $this->unpack(unserialize($data));
    }

    public function unpack($data)
    {
        if ($data instanceof Holder) {
            return $data->closure;
        } elseif (is_array($data)) {
            foreach ($data as $k => &$v) {
                $v = $this->unpack($v);
            }
        }

        return $data;
    }
}

class Holder
{
    protected static $_serializer;

    public static function getSerializer()
    {
        if (self::$_serializer === null) {
            self::$_serializer = new SCS();
        }

        return self::$_serializer;
    }

    public $closure;

    public function __construct($closure)
    {
        $this->closure = $closure;
    }

    public function __sleep()
    {
        $this->closure = self::getSerializer()->serialize($this->closure);
        return ['closure'];
    }

    public function __wakeup()
    {
        $this->closure = self::getSerializer()->unserialize($this->closure);
    }
}
