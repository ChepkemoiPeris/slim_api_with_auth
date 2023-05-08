<?php
namespace App\Controllers;

use App\Response\CustomResponse;
use App\Requests\CustomRequestHandler;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Rules\NotEmpty;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
use \Firebase\JWT\JWT;

class AuthController
{
    protected $customResponse;
    protected $user;
    protected $validator;
    public function __construct()
    {
        $this->customResponse = new CustomResponse;
        $this->user = new User;
        $this->validator = new Validator;
    }

    public function Register(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'name' => v::notEmpty(),
            'email' => v::notEmpty()->email(),
            'password' => v::notEmpty()
        ]);
        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }
        if ($this->EmailExists(CustomRequestHandler::getParam($request, 'email'))) {
            $responseMessage = "This email already exist!";
            return $this->customResponse->is400Response($response, $responseMessage);
        }
        $passwordhash = $this->hashPassword(CustomRequestHandler::getParam($request, 'password'));
        $this->user->create([
            "name" => CustomRequestHandler::getParam($request, "name"),
            "email" => CustomRequestHandler::getParam($request, "email"),
            "password" => $passwordhash
        ]);
        $responseMessage = "New user created successfully";
        return $this->customResponse->is200Response($response, $responseMessage);
    }
    public function EmailExists($email)
    {
        $count = $this->user->where('email', $email)->count();
        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    public function Login(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'email' => v::notEmpty()->email(),
            'password' => v::notEmpty()
        ]);
        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }
        $verifyAccount = $this->verifyAccount(CustomRequestHandler::getParam($request, 'password'), CustomRequestHandler::getParam($request, 'email'));
        if ($verifyAccount == false) {
            $responseMessage = "Invalid email or password";
            return $this->customResponse->is400Response($response, $responseMessage);
        }
        $responseMessage = $this->generateToken(CustomRequestHandler::getParam($request,'email'));
        return $this->customResponse->is200Response($response, $responseMessage);
    }

    public function verifyAccount($password, $email)
    {
        $hashpassword = "";
        $count = $this->user->where('email', $email)->count();
        if ($count == false) {
            return false;
        }
        $user = $this->user->where('email', $email)->get();
        foreach ($user as $users) {
            $hashpassword = $users->password;
        }
        $verify = password_verify($password, $hashpassword);
        if ($verify == false) {
            return false;
        }
        return true;
    }
    public static function generateToken($email)
    {
        $now = time();
        $future = strtotime('+1 hour', $now); 
        $secret = GenerateTokenController::JWT_SECRET_KEY;

        $payload = [
            "jti" => $email,
            "iat" => $now,
            "exp" => $future
        ];

        return JWT::encode($payload, $secret, "HS256");
    }

}