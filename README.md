# yii2-api

Instruções de uso, configuração e detalhes da API:

1) Configuração
- git clone git@github.com:giosf/yii2-api.git
- composer install
- criar arquivo .env conforme este modelo:

    DB_DSN=mysql:host=localhost;dbname=febacapital
    DB_DATABASE=febacapital
    DB_USERNAME=root
    DB_PASSWORD=root
    YII_DEBUG=true
    YII_ENV=dev

- executar ./yii serve

2) Comando de terminal para cadastro de usuário:

Executar o seguinte comando com 3 argumentos: nome, username e senha.

php yii user/create 'Pedro Augusto da Silva' 'pedroas' 'pedro.augusto@gmail.com'

O script criará um usuário se seguintes condições forem cumpridas:
- o username não foi utilizado;
- se todos os argumentos estão presentes.

O script retorna uma string representando um json que contém 'access_token' 'refresh_token' 'expires_in'. Exemplo:

{"access_token":"hPnljI6FU0ckYMQk19X7XuXU3XslFIsC","refresh_token":"kq0VueyK2e9QQ6zhRevS4uus6eGxxKZG","expires_in":1726602423}

3) endpoints da api:

- GET /clients/index

    Request Headers:
    key: "Accept",  value: "application/json; q=1.0, */*; q=0.1"
    key: "Authorization", value: "Bearer <access_token>

    Body:
    "{
        "sort": "name",
        "page": 1,
        "resultsPerPage": 100
    }"

- POST clients/create
    Request Headers:
    key: "Accept",  value: "application/json; q=1.0, */*; q=0.1"
    key: "Authorization", value: "Bearer <access_token>

    Body
    "{
        "name": "nome",
        "cpf": "98723498734",
        "cep": "36046440",
        "address": "Cortes Vilela",
        "number": "904",
        "city": "Juiz de Fora",
        "state": "MG",
        "complement": "casa amarela",
        "sex": "M"
    }"

- GET books/index
    Request Headers:
    key: "Accept",  value: "application/json; q=1.0, */*; q=0.1"
    key: "Authorization", value: "Bearer <access_token>

    Body:
    "{
        "sort": "asdasd",
        "page": 1,
        "resultsPerPage": 25
    }"

- POST /books/create
    Request Headers:
    key: "Accept",  value: "application/json; q=1.0, */*; q=0.1"
    key: "Authorization", value: "Bearer <access_token>

    Body (1): Nesta opção, o sistema importa os dados de autor e título com validação de ISBN 13.
    {
        "isbn": "9786553629318"
    }   

    Body (2): Nesta opção, os dados são inseridos manualmente com validação de ISBN 13
    "{
        "isbn": "9788420634494",
        "title": "Grande Sertão Veredas",
        "author": "Guimarães Rosa",
        "price": "15.00",
        "stock": "10"
    }"

- POST auth/signin

    Request Headers:
    key: "Accept",  value: "application/json; q=1.0, */*; q=0.1"

    Body:
    "{
        "name": "nome",
        "username": "nomedeusuario",
        "password": "98723498734"
    }"

- GET auth/login

    Request Headers:
    key: "Accept",  value: "application/json; q=1.0, */*; q=0.1"

    Body:
    "{
        "username": "newusername",
        "password": "newpassword"
    }"
