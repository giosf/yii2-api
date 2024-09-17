<?php

namespace app\controllers;

use app\models\Book;
use app\models\dao\BookDAO;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpBasicAuth;
use Yii;

/**
 * BooksController implements the CRUD actions for Book model.
 */
class BooksController extends Controller
{

    public $enableCsrfValidation = false;
    public $modelClass = 'app\models\Book';
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
     * Lists Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dao = new BookDAO();
        $params = Yii::$app->request->getBodyParams();
        $dataProvider = $dao->search($params);

        return $this->asJson($dataProvider);
    }

    /**
     * Creates a new Book model.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if ($this->request->isPost)
        {
            $params = Yii::$app->request->getBodyParams();
            $dao = new BookDAO();

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
