<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%clients}}`.
 */
class m240914_192729_create_clients_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%clients}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'cpf' => $this->string(11)->unique(),
            'cep' => $this->integer(8),
            'address' => $this->string(255),
            'number' => $this->integer(6),
            'city' => $this->string(255),
            'state' => $this->string(2),
            'complement' => $this->string(255),
            'sex' => $this->string(1),
        ]);

        $this->createIndex(
            'idx-clients-name',
            '{{%clients}}',
            'name'
        );
        
        $this->createIndex(
            'idx-clients-city',
            '{{%clients}}',
            'city'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%clients}}');
    }
}
