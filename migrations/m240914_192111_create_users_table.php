<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m240914_192111_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'username' => $this->string(255)->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'access_token' => $this->string()->unique(),
            'refresh_token' => $this->string()->unique(),
            'token_expire_time' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
