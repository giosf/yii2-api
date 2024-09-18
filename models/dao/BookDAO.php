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
        Yii::$app->response->format = 'json';
        $model = new Book();
        $existingISBN = Book::find()->andFilterWhere(['isbn' => $params['isbn']])->all();

        if(count($existingISBN))
        {
            throw new BadRequestHttpException('ISBN has already been taken');
        }

        if(count($params) == 1 && isset($params['isbn']))
        {
            $curl = new curl\Curl();
            $response = json_decode($curl->get('https://brasilapi.com.br/api/isbn/v1/' . $params['isbn']));
            if(isset($response->message))
            {
                throw new BadRequestHttpException($response->message);
            }
            else
            {
                $bookData = [
                    'isbn' => $params['isbn'],
                    'author' => implode(',', $response->authors),
                    'title' => $response->title,
                ];

                $model->attributes = $bookData;
            }    
        }
        else
        {
            $inexistentParams = array_diff_key($params, $model->attributeLabels());

            if(count($inexistentParams))
            {
                throw new BadRequestHttpException('Invalid attributes: ' . implode(' - ' , array_keys($inexistentParams)));
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
    
                throw new BadRequestHttpException('Invalid attributes: ' . $content);
            }    
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
            throw new ServerErrorHttpException("Resource not created");
        }
    }

    public function search($params)
    {
        Yii::$app->response->format = 'json';
        foreach ($params as $key => $value)
        {
            if (!in_array($key, $this->getFields()) && !in_array($key, ['sort']))
            {
                throw new BadRequestHttpException("Invalid parameter: $key");
            }

            if ($key === 'sort' &&
                !in_array($value, $this->getFields()['sort']))
            {
                throw new BadRequestHttpException("Invalid sort parameter value: $value");
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
                throw new BadRequestHttpException('Sort parameter not available');
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
            'resultsPerPage',
            'page'
        ];
    }
}
