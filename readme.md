# Laravel com autenticação utilizando jwt-auth

Modelo simplificado de atenticação através de [jwt](https://jwt.io/) utilizando o framework [Laravel](http://laravel.com) (versão 5.2) e [jwt-auth por Sean Tymon](https://github.com/tymondesigns/jwt-auth) (versão 0.5.9).

# Passo a Passo

## Instalação

1. Pacotes iniciais
`laravel new laravel-jwt-auth`
`composer require tymon/jwt-auth`

2. Configurações básicas de instalação do jwt-auth

No arquivo _./config/app.php_:
2.1. Providers
- 'Tymon\JWTAuth\Providers\JWTAuthServiceProvider'

2.2. Aliases
- 'JWTAuth' => 'Tymon\JWTAuth\Facades\JWTAuth'
- 'JWTFactory' => 'Tymon\JWTAuth\Facades\JWTFactory'

2.3. Rode os comandos

`php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"`
`php artisan jwt:generate`

Ao gerar a chave de segurança do jwt, que fica armazenada no arquivo _./config/jwt.php_ sugerimos que seja colocado no arquivo _.env_.

## Documentação oficial

1. [Laravel](http://laravel.com/docs).
2. [jwt-auth wiki](https://github.com/tymondesigns/jwt-auth/wiki)

## Licença

Este model está sob a licença [MIT license](http://opensource.org/licenses/MIT), tanto o Laravel e jwt-auth também possuem a mesma licença.
