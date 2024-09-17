# yii2-api

Instruões de uso, configuração e detalhes da API:

1) Comando de terminal para cadastro de usuário:

Executar o seguinte comando com 3 argumentos: nome, username e senha.

php yii user/create 'Pedro Augusto da Silva' 'pedroas' 'pedro.augusto@gmail.com'

O script criará um usuário se seguintes condições forem cumpridas:
- o username não foi utilizado;
- se todos os argumentos estão presentes.

O script retorna uma string representando um json que contém 'access_token' 'refresh_token' 'expires_in'. Exemplo:

{"access_token":"hPnljI6FU0ckYMQk19X7XuXU3XslFIsC","refresh_token":"kq0VueyK2e9QQ6zhRevS4uus6eGxxKZG","expires_in":1726602423}

2) endpoints da api:

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

    Body:
    "{
        "name": "nome",
        "cpf": "98723498734",
        "cep": "36046440",
        "address": "Cortes Vilela",
        "number": "10",
        "city": "Juiz de Fora",
        "state": "MG",
        "complement": "casa amarela",
        "sex": "M"
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
        "username": "gasdio",
        "password": "qwe123qwe123"
    }"