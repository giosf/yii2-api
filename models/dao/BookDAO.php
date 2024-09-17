<?php

namespace app\models\dao;

use Yii;
use app\models\Book;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use linslin\yii2\curl;

class BookDAO extends Book
{

    public function rules()
    {
        return [
            [['price', 'stock', 'isbn', 'title', 'author', 'page', 'limit', 'sort', 'offset'], 'safe'],
        ];
    }

    public function create($params)
    {
        $model = new Book();
        $inexistentParams = array_diff_key($params, $model->attributeLabels());

        if(count($inexistentParams))
        {
            $response = Yii::$app->response;
            $response->statusCode = 400;
            $response->content = 'Invalid attributes: ' . implode(' - ' , array_keys($inexistentParams));
            $response->format = \yii\web\Response::FORMAT_JSON;

            return $response;
        }

        $model->attributes = $params;

        if (!$model->validate())
        {
            $errors = $model->errors;
            $content = '';
            foreach(array_keys($errors) as $key)
            {
                $content .= $key . ': ' . implode(',', $errors[$key]) . ' ';
            }
            $response = Yii::$app->response;
            $response->statusCode = 400;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->content = json_encode(['message' => 'Invalid attributes: ' . $content]);

            return $response;
        }

        $existingISBN = Book::find()->andFilterWhere(['isbn' => $params['isbn']])->all();
        if(count($existingISBN))
        {
            $response = Yii::$app->response;
            $response->statusCode = 400;
            $response->content = json_encode(['message' => 'ISBN has already been taken']);
            $response->format = \yii\web\Response::FORMAT_JSON;

            return $response;
        }

        $curl = new curl\Curl();
        $response = json_decode($curl->get('https://brasilapi.com.br/api/isbn/v1/' . $params['isbn']));
        if(isset($response->message))
        {
            $curlMessage = $response->message;
            $response = Yii::$app->response;
            $response->statusCode = 400;
            $response->content = json_encode(['message' => $curlMessage]);
            $response->format = \yii\web\Response::FORMAT_JSON;

            return $response;
        }

        if ($model->save())
        {
            $response = Yii::$app->response;
            $response->statusCode = 201;
            $response->content = "Resource created";
            $response->format = \yii\web\Response::FORMAT_JSON;

            return $response;
        }
        else
        {
            $response = Yii::$app->response;
            $response->statusCode = 500;
            $response->content = "Resource not created";
            $response->format = \yii\web\Response::FORMAT_JSON;

            return $response;
        }
    }

    public function search($params)
    {
        foreach ($params as $key => $value)
        {
            $response = Yii::$app->response;
            $response->statusCode = 400;
            $response->format = \yii\web\Response::FORMAT_JSON;

            if (!in_array($key, $this->getFields()) && !in_array($key, ['sort']))
            {
                $response->content = "Invalid parameter: $key";

                return $response;
            }

            if ($key === 'sort' &&
                !in_array($value, $this->getFields()['sort']))
            {
                $response->content = "Invalid sort parameter value: $value";

                return $response;
            }
        }

        $query = Book::find();

        if (isset($params['title']))
        {
            $query->andFilterWhere(['like', 'title', $params['title']]);
        }

        if (isset($params['author']))
        {
            $query->andFilterWhere(['like', 'author', $params['author']]);
        }

        if (isset($params['isbn']))
        {
            $query->andFilterWhere(['isbn' => $params['isbn']]);
        }

        if (isset($params['sort']))
        {
            if (in_array($params['sort'], $this->getFields()['sort']))
            {
                $query->addOrderBy([$params['sort'] => SORT_ASC]);
            }
            else
            {
                $response = Yii::$app->response;
                $response->statusCode = 400;
                $response->content = 'Sort parameter not available';
                $response->format = \yii\web\Response::FORMAT_JSON;

                return $response;
            }
        }

        if(isset($params['resultsPerPage']) && isset($params['page']))
        {
            $resultsPerPage = $params['resultsPerPage'];
            $page = $params['page'];
            $offset = ($page * $resultsPerPage) - $resultsPerPage;
            $limit = $resultsPerPage;
            $query->offset($offset)
                ->limit($limit);
        }

        return $query->all();
    }

    private function getFields()
    {
        return [
            'sort' => ['title', 'price'],
            'isbn',
            'title',
            'author',
        ];
    }
}
