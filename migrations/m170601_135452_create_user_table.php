<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m170601_135452_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'password_hash' => $this->string(),
            'auth_key' => $this->string(),
            'name' => $this->string(),
            'avatar_url' => $this->string()->defaultValue(null),
            'avatar_filename' => $this->string()->defaultValue(null),
            'role_id' => $this->integer(),
            'status_id' => $this->integer(),
            'preferred_language' => $this->string()->defaultValue(null),
            'last_login_at' => $this->dateTime(),
            'last_online_at' => $this->dateTime(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by_id' => $this->integer(),
            'updated_by_id' => $this->integer()
        ]);

        $this->insert('user',[
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('123456'),
            'name' => 'John Johnson',
            'role_id' => \app\helpers\Constants::ROLE_ADMIN,
            'status_id' => \app\helpers\Constants::USR_STATUS_ENABLED,
            'created_at' => date('Y-m-d H:i:s',time()),
            'updated_at' => date('Y-m-d H:i:s',time()),
            'last_login_at' => date('Y-m-d H:i:s',time()),
            'last_online_at' => date('Y-m-d H:i:s',time()),
            'created_by_id' => 0,
            'updated_by_id' => 0,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
