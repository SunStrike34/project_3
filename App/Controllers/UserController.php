<?php

namespace App\Controllers;

use Delight\Auth\Auth;
use PDO;
use App\Controllers\RenderController;

class UserController
{
    private $auth, $db=null;

    public function __construct(PDO $db, Auth $auth)
    {
        $this->db = $db;
        $this->auth = $auth;

    }
    public function login()
    {
        try {
            $this->auth->login($_POST['email'], $_POST['password']);

            RenderController::redirect('/users');
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Wrong email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Wrong password');
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            die('Email not verified');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    public function logOut()
    {
        $this->auth->logOut();
        RenderController::redirect('/login');
    }

    public function register()
    {
        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password']);
        
            RenderController::redirect('/users');
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    public function createUser()
    {
        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password']);
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }




    }
}