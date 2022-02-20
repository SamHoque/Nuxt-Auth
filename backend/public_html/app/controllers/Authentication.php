<?php

namespace App\Controllers;

use App\Models\User;
use Slim\Psr7\Message;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Handles everything for the authentication.
 * @author Sam Hoque
 */
class Authentication
{

    /**
     * Entry point of our login API, this will verify if the user exists in our database
     * If the user exists, it'll compare the password hash against the password we have received
     * @param Request $request
     * @param Response $response
     * @return Message|Response
     */
    public function login(Request $request, Response $response)
    {
        $parsedBody = json_decode($request->getBody()->getContents(), true);
        $responseObject = ['success' => true];
        if (empty($parsedBody)) {
            $responseObject['error'] = 'Please enter username & password';
        } else {
            if (!array_key_exists('username', $parsedBody) || !array_key_exists('password', $parsedBody)) {
                $responseObject['error'] = 'Please enter username & password';
            } else {
                $user = User::where('username', $parsedBody['username'])->first();
                if(!$user || !password_verify($parsedBody['password'], $user->pass_hash)) {
                    $responseObject['error'] = 'Incorrect username or password ';
                } else {
                    $_SESSION['user'] = $user->id;
                }
            }
        }
        $response->getBody()->write(json_encode($responseObject));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Entry point of our login API, this will verify if the user exists in our database
     * If the user exists, it'll compare the password hash against the password we have received
     *
     * TODO: YOU WILL NEED TO ADD ERROR LOGGING FOR PROD!!!
     * @param Request $request
     * @param Response $response
     * @return Message|Response
     */
    public function register(Request $request, Response $response) {
        $parsedBody = json_decode($request->getBody()->getContents());
        $user = User::where('username', $parsedBody->username)->exists();
        if(!$user) {
            $user = User::create([
                'username' => $parsedBody->username,
                'pass_hash' => password_hash($parsedBody->password, PASSWORD_BCRYPT)
            ]);
            $_SESSION['user'] = $user->id;
            $response->getBody()->write(json_encode(['success' => true]));
        } else {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'User already exists']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Unsets the user variable in the session, so we are no longer logged in.
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function logout(Request $request, Response $response) {
        unset($_SESSION['user']);
        return $response->withStatus(200);
    }
}