<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-9-27
 * Time: 下午1:53
 */

namespace app\models;

use app\base_models\System as Fa_System;
use Yii;
use yii\helpers\ArrayHelper;

class System extends Fa_System
{
    public $cache;

    public function init()
    {
        parent::init();
        $this->cache = Yii::$app->cache;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            $cache = Yii::$app->cache;
            $cache->set('all_systems', []);
            $cache->set('usable_systems', []);
            return true;
        }else {
            return false;
        }
    }

    /**
     * 获取全部系统
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public static function getAllSystems()
    {
        $cache = Yii::$app->cache;
        if ($cache->get('all_systems')){
            return $cache->get('all_systems');
        }
        $data = self::find()->asArray()->orderBy('sort')->all();
        $data = ArrayHelper::map($data, 'id', 'name');
        $cache->set('all_systems', $data, 86400);
        return $data;
    }

    /**
     * 获取可用系统
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getUsableSystems()
    {
        $cache = Yii::$app->cache;
        if ($cache->get('usable_systems')){
            return $cache->get('usable_systems');
        }
        $data = self::find()->asArray()->where('`status` = 1')->orderBy('sort')->all();
        $data = ArrayHelper::map($data, 'id', 'name');
        $cache->set('usable_systems', $data, 86400);
        return $data;
    }
}