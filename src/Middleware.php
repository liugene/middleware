<?php

// +----------------------------------------------------------------------
// | LinkPHP [ Link All Thing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://linkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liugene <liujun2199@vip.qq.com>
// +----------------------------------------------------------------------
// |               配置类
// +----------------------------------------------------------------------

namespace linkphp\middleware;

use framework\Exception;
use framework\interfaces\MiddlewareInterface;
use Closure;

class Middleware implements MiddlewareInterface
{

    protected $beginMiddleware = [];

    protected $appMiddleware = [];

    protected $modelMiddleware = [];

    protected $controllerMiddleware = [];

    protected $actionMiddleware = [];

    protected $destructMiddleware = [];

    private $validateMiddle = [
        'beginMiddleware',
        'appMiddleware',
        'modelMiddleware',
        'controllerMiddleware',
        'actionMiddleware',
        'destructMiddleware'
    ]; 

    public function import($middle)
    {
        if(is_array($middle)){
            foreach($middle as $tag => $handle){
                if(empty($handle)){
                    break;
                }
                if($this->isValidate($tag)){
                    $this->add($tag,$handle);
                } else {
                    throw new Exception('不是合法的中间件');
                }
            }
        }
        return $this;
    }

    public function add($tag,$handle)
    {
        if($handle instanceof Closure){
            $handleClosure[] = $handle;
            $this->$tag = array_merge($this->$tag,$handleClosure);
            return;
        }
        $this->$tag = array_merge($this->$tag,$handle);
    }

    public function isValidate($middle)
    {
        return in_array($middle,$this->validateMiddle);
    }

    public function beginMiddleware($middle=null)
    {
        if(!is_null($middle)){
            $this->add('beginMiddleware',$middle);
            return;
        }
        return $this->target('beginMiddleware');
}

    public function appMiddleware($middle=null)
    {
        if(!is_null($middle)){
            $this->add('appMiddleware',$middle);
            return;
        }
        return $this->target('appMiddleware');
    }

    public function modelMiddleware($middle=null)
    {
        if(!is_null($middle)){
            $this->add('modelMiddleware',$middle);
            return;
        }
        return $this->target('modelMiddleware');
    }

    public function controllerMiddleware($middle=null)
    {
        if(!is_null($middle)){
            $this->add('controllerMiddleware',$middle);
            return;
        }
        return $this->target('controllerMiddleware');
    }

    public function actionMiddleware($middle=null)
    {
        if(!is_null($middle)){
            $this->add('actionMiddleware',$middle);
            return;
        }
        return $this->target('actionMiddleware');
    }

    public function destructMiddleware($middle=null)
    {
        if(!is_null($middle)){
            $this->add('destructMiddleware',$middle);
            return;
        }
        return $this->target('destructMiddleware');
    }

    public function target($middle)
    {
        if($this->$middle){
            array_walk_recursive($this->$middle,[$this, 'exec']);
        }
    }

    public function exec($value,$key)
    {
        if($value instanceof Closure){
            return call_user_func($value);
        }
        return call_user_func([new $value,'handle']);
    }

}
