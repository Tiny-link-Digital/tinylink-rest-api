# tiny.link REST API
Escrita em Laravel + MongoDB, a tiny.link REST API provê o serviço de encurtamento de links em hashes únicas.


## Autenticação
O processo de autenticação é feito através da biblioteca composer [google/apiclient](https://packagist.org/packages/google/apiclient), mais precisamente, deve-se enviar o token obtido através da integração com o [Google Social Login](https://developers.google.com/identity/sign-in/web/sign-in), da seguinte forma:
### *POST /api/user/signin
```json
//Request
{
  "id_token": "{token-do-login-social}"
}

//Response
{
  "_id": "60da876fa0cde53e335d2112",
  "id": "692091097750299102245",
  "email": "johnnylawrence@cobrakai.com",
  "name": "Johnny Lawrence",
  "picture": "https:\/\/lh3.googleusercontent.com\/a-\/AOh14GgIurjlJZaNXmIrBs2VvnAfgyUWuihOKNs-l4sQVg=s96-c",
  "updated_at": "2021-07-25T20:22:08.953000Z",
  "created_at": "2021-06-29T02:37:35.844000Z",
  "app_key": "60fdc7f0e88a0"
}
```
 Para autenticar as rotas de geração e listagem das URL's de determinado usuário, deve-se enviar o valor de `app_key` no cabeçalho `Authorization`, da seguinte forma:
```
Authorization: Bearer 60fdc7f0e88a0
```
Vale ressaltar que essa credencial é renovada a cada novo processo de login, expirando a chave anteriormente utilizada.

## Cerar URL's encurtadas
### *POST /api/url/create
```json
// Request
{
  "url": "https://www.youtube.com/watch?v=Q2BKQIFvU1I"
}

// Response
{
  "user_id": "109106929950722452097",
  "url": "https:\/\/www.youtube.com\/watch?v=Q2BKQIFvU1I",
  "hash": "b43fa4",
  "expires_at": "2022-07-25T21:05:41.966000Z",
  "updated_at": "2021-07-25T21:05:41.966000Z",
  "created_at": "2021-07-25T21:05:41.966000Z",
  "_id": "60fdd225b59caa005f67e882"
}
```

## Obter uma URL encurtada
### *GET /api/url/:hash
```json
//Response
{
  "_id": "60fdc9454abe570a892c3c92",
  "user_id": "109106929950722452097",
  "url": "https:\/\/www.adorocinema.com\/noticias\/series\/noticia-159757\/",
  "hash": "asdsdf12",
  "expires_at": "2022-07-25T20:27:49.925000Z",
  "updated_at": "2021-07-25T20:27:49.925000Z",
  "created_at": "2021-07-25T20:27:49.925000Z"
}
```

## Listar as URL's de determinado usuário
### *GET /api/url/list?page=1
```json
//Response
{
  "current_page": 1,
  "data": [
    {
      "_id": "60fdc8cb61a47c57eb04e542",
      "user_id": "109106929950722452097",
      "url": "https:\/\/www.uol.com.br\/splash\/colunas\/guilherme-ravache\/2021\/07\/22\/netflix-lancara-games-de-series-como-stranger-things-para-voltar-a-crescer.htm",
      "hash": "83e285",
      "expires_at": "2022-07-25T20:25:47.512000Z",
      "updated_at": "2021-07-25T20:25:47.513000Z",
      "created_at": "2021-07-25T20:25:47.513000Z"
    },
    {
      "_id": "60fdc9454abe570a892c3c92",
      "user_id": "109106929950722452097",
      "url": "https:\/\/www.adorocinema.com\/noticias\/series\/noticia-159757\/",
      "hash": "533353",
      "expires_at": "2022-07-25T20:27:49.925000Z",
      "updated_at": "2021-07-25T20:27:49.925000Z",
      "created_at": "2021-07-25T20:27:49.925000Z"
    },
    {
      "_id": "60fdd1b04abe570a892c3c93",
      "user_id": "109106929950722452097",
      "url": "https:\/\/www.youtube.com\/watch?v=Q2BKQIFvU1I",
      "hash": "8be98e",
      "expires_at": "2022-07-25T21:03:44.394000Z",
      "updated_at": "2021-07-25T21:03:44.395000Z",
      "created_at": "2021-07-25T21:03:44.395000Z"
    },
    {
      "_id": "60fdd225b59caa005f67e882",
      "user_id": "109106929950722452097",
      "url": "https:\/\/www.youtube.com\/watch?v=Q2BKQIFvU1I",
      "hash": "b43fa4",
      "expires_at": "2022-07-25T21:05:41.966000Z",
      "updated_at": "2021-07-25T21:05:41.966000Z",
      "created_at": "2021-07-25T21:05:41.966000Z"
    }
  ],
  "first_page_url": "http:\/\/tinylink.stg\/api\/url\/list?page=1",
  "from": 1,
  "last_page": 1,
  "last_page_url": "http:\/\/tinylink.stg\/api\/url\/list?page=1",
  "links": [
    {
      "url": null,
      "label": "&laquo; Previous",
      "active": false
    },
    {
      "url": "http:\/\/tinylink.stg\/api\/url\/list?page=1",
      "label": "1",
      "active": true
    },
    {
      "url": null,
      "label": "Next &raquo;",
      "active": false
    }
  ],
  "next_page_url": null,
  "path": "http:\/\/tinylink.stg\/api\/url\/list",
  "per_page": 9,
  "prev_page_url": null,
  "to": 4,
  "total": 4
}
```

