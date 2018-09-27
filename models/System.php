<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-9-27
 * Time: 下午1:53
 */

namespace app\models;

use app\base_models\System as Fa_System;

class System extends Fa_System
{
    public static function getAllSystems()
    {
        return self::find()->asArray()->all();
    }

    public static function getUsableSystems()
    {
        return self::find()->asArray()->where('`status` = 1')->all();
    }
}