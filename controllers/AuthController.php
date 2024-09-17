<?php

namespace app\controllers;

use app\models\User;
use yii\filters\auth\HttpBearerAuth;
use Yii;

class AuthController extends \yii\web\Controller
{
	// {"access_token":"CmdMSu5dlmdNtGLxiwfrhRTNqTsoPTlP","refresh_token":"BN0Axqc7Wro_mEkxka46UY7a7j7SeNnd","expires_in":1726556738}
    public $enableCsrfValidation = false;

    public function behaviors() {
    	$behaviors = parent::behaviors();

		$behaviors['authenticator'] = [
			'class' => HttpBearerAuth::className(),
			'except' => ['signin', 'login'],
		];

		return $behaviors;
	}

	public function actionSignin()
	{
		$request = Yii::$app->request;
		$name = $request->post('name');
		$username = $request->post('username');
		$password = $request->post('password');
	
		if (empty($username) || empty($password))
		{
			throw new BadRequestHttpException('Username and password cannot be blank.');
		}
	
		$existingUser = User::findOne(['username' => $username]);
		if ($existingUser)
		{
			throw new BadRequestHttpException('Username already exists.');
		}
	
		$user = new User();
		$user->username = $username;
		$user->setPassword($password);
		$user->access_token = User::generateAccessToken();
		$user->refresh_token = User::generateRefreshToken();
		$user->token_expire_time = time() + (60 * 60); // 1 hour
	
		if ($user->save())
		{
			return json_encode([
				'access_token' => $user->access_token,
				'refresh_token' => $user->refresh_token,
				'expires_in' => $user->token_expire_time,
			]);
		}
	
		return [
			'errors' => $user->errors,
		];
	}

	public function actionLogin()
	{
		$request = Yii::$app->request;
		$name = $request->post('name');
		$username = $request->post('username');
		$password = $request->post('password');

		$user = User::findByUsername($username);
		if ($user && $user->validatePassword($password))
		{
			$user->access_token = User::generateAccessToken();
			$user->refresh_token = User::generateRefreshToken();
			$user->token_expire_time = time() + (60 * 60);

			if ($user->save())
			{
				return json_encode([
					'access_token' => $user->access_token,
					'refresh_token' => $user->refresh_token,
					'expires_in' => $user->token_expire_time,
				]);
			}
		}

		throw new \yii\web\UnauthorizedHttpException('Invalid credentials.');
	}

	public function actionRefresh()
	{
		$request = Yii::$app->request;
		$refreshToken = $request->post('refresh_token');

		$user = User::findOne(['refresh_token' => $refreshToken]);
		if ($user)
		{
			$user->access_token = User::generateAccessToken();
			$user->token_expire_time = time() + (60 * 60);

			if ($user->save()) {
				return [
					'access_token' => $user->access_token,
					'expires_in' => $user->token_expire_time,
				];
			}
		}

		throw new \yii\web\UnauthorizedHttpException('Invalid refresh token.');
	}

}
