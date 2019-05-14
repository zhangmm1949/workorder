<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Test extends Model
{
    public static $name = [];

    public static function getName()
    {
        if(empty(self::$name)){
            self::$name['key'] = 'name';
            self::$name['value'] = 'zhangmm';
        }
        
        return self::$name;
    }

    // public function setName()
    // {
    //     if (empty(self::$name)){
    //         self::$name['key'] = 'age';
    //         self::$name['value'] = '12';
    //     }
    //     return self::$name;
    // }

    public function getName2()
    {
        return self::$name;
    }
}

class Test2 extends Model
{
    public $name = [];

    public function getName()
    {
        if(empty($this->name)){
            $this->name['key'] = 'name';
            $this->name['value'] = 'zhangmm';
        }
        
        return $this->name;
    }

    // public function setName()
    // {
    //     if (empty($this->name)){
    //         $this->name['key'] = 'age';
    //         $this->name['value'] = '12';
    //     }
    //     return $this->name;
    // }

    public function getName2()
    {
        return $this->name;
    }

}