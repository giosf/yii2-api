<?php

namespace app\models\dao;

use Yii;
use app\models\Client;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use linslin\yii2\curl;

class ClientDAO extends Client
{
    public function rules()
    {
        return [
            [['name', 'cpf', 'page', 'limit', 'sort', 'offset'], 'safe'],
        ];
    }

    public function create($params)
    {
        Yii::$app->response->format = 'json';
        $model = new Client();
        $inexistentParams = array_diff_key($params, $model->attributeLabels());

        if(count($inexistentParams))
        {
            throw new BadRequestHttpException('Invalid attributes: ' . implode(' - ' , array_keys($inexistentParams)));
        }

        $existingCPF = Client::find()->andFilterWhere(['cpf' => $params['cpf']])->all();
        if(count($existingCPF))
        {
            throw new BadRequestHttpException('CPF has already been taken');
        }

        if(!in_array($params['sex'], ['m', 'M', 'f', 'F']))
        {
            throw new BadRequestHttpException('Invalid value for field: sex');
        }

        if(!$this->isCPFValid($params['cpf']))
        {
            throw new BadRequestHttpException('Invalid CPF');
        }

        $curl = new curl\Curl();
        $response = json_decode($curl->get('https://brasilapi.com.br/api/cep/v1/' . $params['cep']));
        if(isset($response->errors))
        {
            throw new BadRequestHttpException('Invalid CEP');
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
            if (!in_array($key, $this->getFields()) && $key != 'sort')
            {
                throw new BadRequestHttpException("Invalid parameter: $key");
            }
    
            if ($key === 'sort' &&
                !in_array($value, $this->getFields()['sort']))
            {
                throw new BadRequestHttpException("Invalid sort parameter value: $value");
            }
        }

        $query = Client::find();

        if (isset($params['name']))
        {
            $query->andFilterWhere(['like', 'name', $params['name']]);
        }

        if (isset($params['cpf']))
        {
            $query->andFilterWhere(['cpf' => $params['cpf']]);
        }

        if (isset($params['sort']))
        {
            $sortOptions = explode(',', $params['sort']);
            foreach ($sortOptions as $sortOption)
            {
                if (in_array($sortOption, $this->getFields()['sort']))
                {
                    $query->addOrderBy([$sortOption => SORT_ASC]);
                }
                else
                {
                    throw new BadRequestHttpException('Sort parameter not available');
                }
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
            'sort' => ['name', 'city', 'cpf'],
            'name',
            'cpf',
            'resultsPerPage',
            'page'
        ];
    }

    public function isCPFValid($cpf)
    {
        $cpf = preg_replace('/[^\d]/', '', $cpf);
    
        if (strlen($cpf) != 11)
        {
            return false;
        }
    
        if (preg_match('/(\d)\1{10}/', $cpf))
        {
            return false;
        }

        for ($t = 9; $t < 11; $t++)
        {
            for ($d = 0, $c = 0; $c < $t; $c++)
            {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
    
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d)
            {
                return false;
            }
        }
    
        return true;
    }
}
