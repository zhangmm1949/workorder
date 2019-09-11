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
use yii\helpers\ArrayHelper;

class UserSystem extends Fa_Class
{

    /**
     * @param $event object Event绑定的事件
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function updateUserSystems($event)
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
                /*
                    $user_system = new UserSystem();
                    foreach ($user->systems as $item){
                        $user_system->user_id = $user->id;
                        $user_system->system_id = $item;
                        $user_system->save();
                    }
                */
                self::insertUserSystems($user->id, $user->systems);
                break;
            case 'update' :
                self::deleteAll(['user_id'=>$user->id]);
                self::insertUserSystems($user->id, $user->systems);
                break;
            case 'delete' :
                self::deleteAll(['user_id'=>$user->id]);
                break;
            default :
                return true;
        }
        return true;
    }

    /**
     * 批量写入用户关联系统
     * @param $user_id
     * @param $systems
     * @return int
     * @throws \yii\db\Exception
     */
    private static function insertUserSystems($user_id, $systems){
        if (!$systems) return true; //注册时systems为空
        $rows = [];
        foreach ($systems as $key => $value){
            $rows[$key]['user_id'] = $user_id;
            $rows[$key]['system_id'] = intval($value);
        }
        return Yii::$app->db->createCommand()->batchInsert(UserSystem::tableName(),['user_id', 'system_id'], $rows)->execute();
    }

    public static function getSystemIdsByUserId($user_id)
    {
        $user_id = intval($user_id);
        $data = array_column(self::find()->asArray()->select('system_id')->where(['user_id'=>$user_id])->all(), 'system_id');
        return $data;
    }

    /**
     * @param int $user_id
     * @return mixed
     * @throws \yii\db\Exception
     */
    public static function getSystemsByUser(int $user_id)
    {
        $sql = "select `id`, `name` from xm_system s inner join xm_user_system us on us.system_id = s.id where us.user_id=:user_id;";
        $ret = Yii::$app->db->createCommand($sql)->bindValue(':user_id', $user_id)->queryAll();
        $data = ArrayHelper::map($ret, 'id', 'name');

        return $data;
    }

    /**
     * 根据用户关联的系统获取用户
     * @param int $system_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getUsersBySystem(int $system_id)
    {
        $sql = "select `id`, `user_name` from xm_user u inner join xm_user_system us on us.user_id = u.id where u.status = 1 and us.system_id=:system_id order by convert(u.`user_name` using gbk);";
        $ret = Yii::$app->db->createCommand($sql)->bindValue(':system_id', $system_id)->queryAll();
        $data = ArrayHelper::map($ret, 'id', 'user_name');

        return $data;
    }
}