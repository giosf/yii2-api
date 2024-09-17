<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m240914_193336_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'isbn' => $this->string(13)->unique(),
            'title' => $this->string(255),
            'author' => $this->string(255),
            'price' => $this->decimal(10,2),
            'stock' => $this->integer(),
        ]);

        $this->createIndex(
            'idx-books-title',
            '{{%books}}',
            'title'
        );

        $this->createIndex(
            'idx-books-price',
            '{{%books}}',
            'price'
        );

        $this->createIndex(
            'idx-books-author',
            '{{%books}}',
            'author'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
