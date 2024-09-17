<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Client;
use app\models\Book;
use Faker\Factory;
use Yii;

class SeederController extends Controller
{
    public function actionIndex()
    {
        $faker = Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\Internet($faker));

        for ($i = 0; $i < 10000; $i++)
        {
            $client = new Client();
            $client->name = $faker->name;
            $client->cpf = (int) $faker->randomNumber(9, true);
            $client->cep = (int) $faker->postcode;
            $client->address = $faker->streetAddress;
            $client->number = $faker->numberBetween(1, 99);
            $client->city = $faker->city;
            $client->state = $faker->stateAbbr;
            $client->complement = $faker->secondaryAddress;
            $client->sex = $faker->randomElement(['M', 'F']);
            if (!$client->save())
            {
                echo "Erro ao salvar cliente: " . json_encode($client->getErrors()) . "\n";
            }
        }

        for ($i = 0; $i < 5000; $i++)
        {
            $book = new Book();
            $book->isbn = $faker->isbn13;
            $book->title = $faker->realText($faker->numberBetween(20, 30));
            $book->author = $faker->name;
            $book->price = $faker->randomFloat(2, 10, 100);
            $book->stock = $faker->randomNumber(2);
            if (!$book->save())
            {
                echo "Erro ao salvar livro: " . json_encode($book->getErrors()) . "\n";
            }
        }

        echo "Seeding terminado.\n";
    }
}