<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $username
 * @property string|null $password_hash
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'username', 'password_hash'], 'string', 'max' => 255],
            [['username'], 'unique'],
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
            'username' => 'Username',
            'password_hash' => 'Password Hash',
        ];
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::findOne(['access_token' => $token]);
        if ($user && $user->token_expire_time < time())
        {
            return null;
        }
        return $user;
    }

    public static function findByUsername($username)
    {
		return static::find()
            ->where(['username' => $username])
			->one();
    }

    public static function findIdentity($id)
    {
		return static::find()
            ->where(['id' => $id])
			->one();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public static function generateAccessToken()
    {
        return Yii::$app->security->generateRandomString();
    }

    public static function generateRefreshToken()
    {
        return Yii::$app->security->generateRandomString();
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }
}
