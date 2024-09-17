<?php

namespace app\controllers;

use app\models\Client;
use app\models\dao\ClientDAO;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpBasicAuth;
use Yii;

/**
 * ClientsController implements the CRUD actions for Client model.
 */
class ClientsController extends Controller
{

    public $enableCsrfValidation = false;
    public $modelClass = 'app\models\Client';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => HttpBearerAuth::className(),
                ],
            ]
        );
    }

    /**
     * Lists Client models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dao = new ClientDAO();
        $params = Yii::$app->request->getBodyParams();
        $dataProvider = $dao->search($params);

        return $this->asJson($dataProvider);
    }

    /**
     * Creates a new Client model.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if ($this->request->isPost)
        {
            $params = Yii::$app->request->getBodyParams();
            $dao = new ClientDAO();

            return $dao->create($params);
        }
        else
        {
            $response = Yii::$app->response;
            $response->statusCode = 405;
            $response->format = \yii\web\Response::FORMAT_JSON;

            return $response;
        }
    }

}
