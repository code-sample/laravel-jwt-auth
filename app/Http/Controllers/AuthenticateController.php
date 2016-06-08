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
