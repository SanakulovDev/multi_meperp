<?php

use app\models\User;
use yii\db\Migration;

/**
 * Class m200429_061451_create_qlik_user
 */
class m200429_061451_create_qlik_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $username = 'qlik';
		$user = new User();
		$user->username = $username;
		$user->tabno = 'qlik';
		$user->account_suffix = Yii::$app->params['account_suffix'];
		$user->fullname = 'Qlik Sense';
		$user->password = 'qlikPassword2020';
		$user->email = 'qlik@gm.uz';
		$user->setPassword($user->password);
        $user->generateAuthKey();
        $user->access_token = '5TIjGUM7KE63yWzu3ksJ5SKrdsUXCqW4qKcGUhdjcNp93aJBjKj/4ylaL9WbtVXraoR/PqxJz1pPMJF9hKTRkA==';
		$user->status = User::STATUS_ACTIVE;
		$user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
    
}
