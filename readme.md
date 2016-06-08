# Laravel com autenticação utilizando jwt-auth

Modelo simplificado de atenticação através de [jwt](https://jwt.io/) utilizando o framework [Laravel](http://laravel.com) (versão 5.2) e [jwt-auth por Sean Tymon](https://github.com/tymondesigns/jwt-auth) (versão 0.5.9).

# Passo a Passo

## Instalação

### Pacotes iniciais
`laravel new laravel-jwt-auth`

`composer require tymon/jwt-auth`

### Configurações básicas de instalação do jwt-auth

No arquivo _./config/app.php_:

#### Providers
`'Tymon\JWTAuth\Providers\JWTAuthServiceProvider'`

#### Aliases
`'JWTAuth' => 'Tymon\JWTAuth\Facades\JWTAuth'`
`'JWTFactory' => 'Tymon\JWTAuth\Facades\JWTFactory'`

#### Rode os comandos

`php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"`
`php artisan jwt:generate`

Ao gerar a chave de segurança do jwt, que fica armazenada no arquivo _./config/jwt.php_ sugerimos que seja colocado no arquivo _.env_.

### Rode a função do Laravel para criar as informações do usuário

`php artisan make:auth`

Crie um banco de dados para teste e configure o aquivo _.env_ e faça a migração das tabelas usuários.

`php artisan migrate`


## Configurações

### Configurações gerais do jwt-auth

#### Crie o controller _AuthenticateController_:

`php artisan make:controller AuthenticateController`

Neste controller deve estar mais ou menos assim, conforme sua demanda:

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        // Buscar credenciais através da variável Request
        $credentials = $request->only('email', 'password');

        try {
        	// Há várias formas de gerar o token
        	//
        	// 1. Utilizando as credenciais de um usuário
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        	// 2. Utilizando o objeto $user (modelo User)
			// $user = User::first();
			// $token = JWTAuth::fromUser($user);

        	// 3. Utilizanr qualquer informação que deseje
        	// $customClaims = ['foo' => 'bar', 'baz' => 'bob'];
			// $payload = JWTFactory::make($customClaims);
			// $token = JWTAuth::encode($payload);

        // Retorna o token se estiver ok
        return response()->json(compact('token'));
    }

    public function getAuthenticatedUser()
	{
	    try {

	        if (! $user = JWTAuth::parseToken()->authenticate()) {
	            return response()->json(['user_not_found'], 404);
	        }

	    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

	        return response()->json(['token_expired'], $e->getStatusCode());

	    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

	        return response()->json(['token_invalid'], $e->getStatusCode());

	    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

	        return response()->json(['token_absent'], $e->getStatusCode());

	    }

	    // the token is valid and we have found the user via the sub claim
	    return response()->json(compact('user'));
	}
}

```

#### Inclua o jwt-auth middleware

No arquivo _./app/Http/Kernel.php_:

```
protected $routeMiddleware = [
    ...
    'jwt.auth' => 'Tymon\JWTAuth\Middleware\GetUserFromToken',
    'jwt.refresh' => 'Tymon\JWTAuth\Middleware\RefreshToken',
];
```

#### Atualize o _handler_ para exceções

No arquivo _app/Exceptions/Handler.php_, função `render`:

```
if ($e instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) {
    return response()->json(['token_expired'], $e->getStatusCode());
} else if ($e instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) {
    return response()->json(['token_invalid'], $e->getStatusCode());
}

return parent::render($request, $e);
```

#### Atualize suas rotas para testar suas configurações

No arquivo _./app/Http/routes.php_ insira as rotas abaixo:

```
Route::post('api/auth', 'AuthenticateController@authenticate');
Route::get('getUser', 'AuthenticateController@getAuthenticatedUser');
Route::group(['prefix' => 'api', 'middleware' => 'jwt.auth'], function() 
{
	// Todas as rotas aqui já deverão ter sido autenticadas!
	Route::get('user', function() 
	{
		return 'Autenticado!';
	});
});
```

## Testes

### Crie um usuário utilizando os formulários básicos do Laravel

> http://localhost:8000/register

Exemplo:
	- email: john.doe@domain.com
	- senha: teste123

### Teste suas funções de criação de token com o [PostMan](http://www.getpostman.com/), sendo:

#### Busque o CSRF-TOKEN

Como o Laravel possui uma camada de segurança utilizando token CSRF para sessões, será necessário buscar esse token para fazer nossa validação via jwt.

- Acesse a página: _http://localhost:8000/login_, vá ao código fonte (Ctrl+U no Chrome).
- Localize a palavra **token**, será um _input_ do tipo _hidden_, e copie seu valor (_value_); 

#### Crie um token

- Método: POST
- URL: http://localhost:8000/api/auth
- Body: 
	- email: john.doe@domain.com (email do seu usuário criado no item 5)
	- password: teste123 (senha de seu usuário criado no item 5)
- Headers: 
	- X-CSRF-TOKEN: **Aqui será colocado seu CSRF-TOKEN**(item 6.1)
- Clique em _Send_, seu resultado será o seu token(jwt-token)!

#### Teste a autenticação com o token

- Método: GET
- URL: http://localhost:8000/getUser
- Headers: 
	- Authorization: Bearer {**seu jwt-token**}
_É, tem de usar a key "Authorization" e no campo value exatamente como acima: "Bearer {seu jwt-token}"!_

Pronto!

Verá que está validando! Teste retirar o token e veja que causará uma exceção.

Verifique as rotas dentro do _middleware_ group e verá que funciona.

Modelos utilizados no PostMan (Collections) estão em json v2 em [_./storage/postman_](https://github.com/code-sample/laravel-jwt-auth/blob/master/storage/postman/Laravel-jwt-auth.postman_collection.json).

## Documentação oficial

1. [Laravel](http://laravel.com/docs).
2. [jwt-auth wiki](https://github.com/tymondesigns/jwt-auth/wiki)

## Licença

Este model está sob a licença [MIT license](http://opensource.org/licenses/MIT), tanto o Laravel e jwt-auth também possuem a mesma licença.
