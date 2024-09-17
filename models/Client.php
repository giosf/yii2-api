<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $cpf
 * @property int|null $cep
 * @property string|null $address
 * @property int|null $number
 * @property string|null $city
 * @property string|null $state
 * @property string|null $complement
 * @property string|null $sex
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cpf', 'cep', 'number'], 'integer'],
            [['name', 'address', 'city', 'complement'], 'string', 'max' => 255],
            [['state'], 'string', 'max' => 2],
            [['sex'], 'string', 'max' => 1],
            [['cpf'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'cpf' => 'Cpf',
            'cep' => 'Cep',
            'address' => 'Address',
            'number' => 'Number',
            'city' => 'City',
            'state' => 'State',
            'complement' => 'Complement',
            'sex' => 'Sex',
        ];
    }

}
