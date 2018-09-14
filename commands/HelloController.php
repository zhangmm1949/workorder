<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }

    public function actionSendMail()
    {
        $mail= \Yii::$app->mailer->compose();
        $mail->setTo('704369798@qq.com'); //要发送给那个人的邮箱
        $mail->setSubject("异步发送邮件"); //邮件主题
//        $mail->setTextBody('测试text'); //发布纯文字文本 //无法发送
        $mail->setHtmlBody("测试html 异步"); //发送的消息内容
        var_dump($mail->send());
    }

    public function actionSendMails()
    {
        $users = ['704369798@qq.com','704369798@qq.com','704369798@qq.com'];
        $messages = [];
        foreach ($users as $k => $user) {
            $messages[] = \Yii::$app->mailer->compose()
                ->setTo($user)
                ->setSubject('测试主题' . $k)
                ->setHtmlBody('测试内容' . $k);
        }
        \Yii::$app->mailer->sendMultiple($messages);
    }
}
