<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-9-1
 * Time: 下午2:01
 */

namespace app\models;

use app\base_models\UserSystem as Fa_Class;
use Yii;
use yii\base\Event;

class UserSystem extends Fa_Class
{
    /**
     * @param $event Event 绑定的事件
     * @throws \yii\db\Exception
     */
    public function updateUserSystems($event)
    {
        $user = $event->sender;
        $action = $event->data;
        switch ($action){
            case 'insert' :
                /**
                 * 错误写法示例：
                 * 这样写，foreach 中操作的其实是同一个对象，所以只会保存最新一条记录。
                 * 可以每次循环都 new Class , 也可以按下面批量写入方式（insertUserSystems()）
                 */
                /*foreach ($user->systems as $item){
                    $user_system->user_id = $user->id;
                    $user_system->system_id = $item;
                    $user_system->save();
                }*/
                self::insertUserSystems($user->id, $user->systems);
                break;
            case 'update' :
                self::deleteAll(['user_id'=>$user->id]);
                self::insertUserSystems($user->id, $user->systems);
                break;
            case 'delete' :
                self::deleteAll(['user_id'=>$user->id]);
                break;
        }
    }

    /**
     * 批量写入用户关联系统
     * @param $user_id
     * @param $systems
     * @return int
     * @throws \yii\db\Exception
     */
    private static function insertUserSystems($user_id, $systems){
        $rows = [];
        foreach ($systems as $key => $value){
            $rows[$key]['user_id'] = $user_id;
            $rows[$key]['system_id'] = intval($value);
        }
        return Yii::$app->db->createCommand()->batchInsert(UserSystem::tableName(),['user_id', 'system_id'], $rows)->execute();
    }
}