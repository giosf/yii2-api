<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\User;
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
class UserController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionCreate($name, $username, $password)
    {
        if (empty($name) || empty($username) || empty($password))
		{
			echo "Name, username and password fields are required. \r\n";
            return ExitCode::USAGE;
		}
	
		$existingUser = User::find()
            ->where(['name' => $name])
            ->where(['username' => $username])
            ->all();

        if (count($existingUser))
		{
			echo "Username already exists.\r\n";
            return ExitCode::USAGE;
		}
	
		$user = new User();
		$user->name = $name;
		$user->username = $username;
		$user->setPassword($password);
		$user->access_token = User::generateAccessToken();
		$user->refresh_token = User::generateRefreshToken();
		$user->token_expire_time = time() + (60 * 60); // 1 hour

		if ($user->save())
		{
			echo json_encode([
				'access_token' => $user->access_token,
				'refresh_token' => $user->refresh_token,
				'expires_in' => $user->token_expire_time,
			]) . "\r\n";
		}

        return ExitCode::OK;
    }
}
