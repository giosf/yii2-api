<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string|null $isbn
 * @property string|null $title
 * @property string|null $author
 * @property float|null $price
 * @property int|null $stock
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['price'], 'required'],
            [['stock'], 'integer'],
            [['stock'], 'required'],
            [['title', 'author'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 13],
            [['isbn'], 'unique'],
            [['isbn'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'isbn' => 'Isbn',
            'title' => 'Título',
            'author' => 'Autor',
            'price' => 'Preço',
            'stock' => 'Estoque',
        ];
    }
}
