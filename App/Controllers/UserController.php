<?php

namespace App\Controllers;

use Delight\Auth\Auth;
use App\Controllers\RedirectController;
use App\Controllers\RenderController;
use App\Models\UserModel;
use App\Models\ImageModel;
use Tamtamchik\SimpleFlash\Flash;

class UserController
{
    private $auth, $userModel, $render, $flash, $imageModel;

    public function __construct(Auth $auth, 
                                UserModel $userModel, 
                                RenderController $render, 
                                Flash $flash, 
                                ImageModel $imageModel
                                )
    {
        $this->auth = $auth;
        $this->userModel = $userModel;
        $this->render = $render;
        $this->flash = $flash;
        $this->imageModel = $imageModel;
    }

    public function getPageLogin()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->render->getPage("login");
        } else {
            RedirectController::redirect('/users');
        }
    }

    public function login()
    {
        if (!$this->auth->isLoggedIn()) {
            try {
                 $this->auth->login($_POST['email'], $_POST['password']);
                 $userId = $this->auth->getUserId();
                 $userData = $this->userModel->getUser($userId);
                 $_SESSION['user'] = $userData;
                 $this->flash->success('Successful account login!');
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                $this->flash->error('Wrong email address');
                RedirectController::redirect('/login');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                $this->flash->error('Wrong password');
                RedirectController::redirect('/login');
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                $this->flash->error('Email not verified');
                RedirectController::redirect('/login');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                $this->flash->error('Too many requests');
                RedirectController::redirect('/login');
            }
        }

        RedirectController::redirect('/users');
    }

    public function logOut()
    {
        $_SESSION['user'] = '';
        $this->auth->logOut();
        RedirectController::redirect('/login');
    }

    public function getPageRegister()
    {
        if (!($this->auth->isLoggedIn())) {
            $this->render->getPage("register");
        } else {
            RedirectController::redirect('/users');
        }
    }

    public function register()
    {
        if (!$this->auth->isLoggedIn()) {
            try {
                $this->auth->register($_POST['email'], $_POST['password']);
                $this->flash->success('Successful registration!');
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                $this->flash->error('Invalid email address');
                RedirectController::redirect('/register');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                $this->flash->error('Invalid password');
                RedirectController::redirect('/register');
            }
            catch (\Delight\Auth\UserAlreadyExistsException $e) {
                $this->flash->error('User already exists');
                RedirectController::redirect('/register');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                $this->flash->error('Too many requests');
                RedirectController::redirect('/register');
            }
        }

        RedirectController::redirect('/login'); 
    }

    public function getPageUsers()
    {
        if ($this->auth->isLoggedIn()) {
            $this->render->getPage('users', $this->userModel->getUsers());
        } else {
            RedirectController::redirect('/login');
        }
    }

    public function deleteUser($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) { 
                try {
                    $this->imageModel->deleteImage($data['id']);
                    $this->userModel->deleteDataForUser($data['id']);
                    $this->auth->admin()->deleteUserById($data['id']);
                    if ($_SESSION['auth_user_id'] == $data['id']) {
                        self::logOut();
                    }
                }
                catch (\Delight\Auth\UnknownIdException $e) {
                    $this->flash->error('Unknown ID');
                }
            }
            
            RedirectController::redirect('/users');
        } else {
            RedirectController::redirect('/login');
        }
    }

    public function getPageCreateUser()
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN) { 
                $this->render->getPage("create_user");
            }
            else {
                RedirectController::redirect('/users');
            }
        }

        RedirectController::redirect('/login');
    }

    public function createUser()
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN) {

                try {
                    $userId = $this->auth->admin()->createUser($_POST['email'], $_POST['password'], (!empty($_POST['username']) ? $_POST['username'] : null));
                    $data = [
                        'user_id' =>$userId,
                        'role' => \Delight\Auth\Role::CONSUMER
                    ];
                    $this->userModel->insert('users_info', $data);
                    //d($userId, $_POST, $_FILES); die();
                }
                catch (\Delight\Auth\InvalidEmailException $e) {
                    $this->flash->error('Invalid email address');
                    RedirectController::redirect('/create_user');
                }
                catch (\Delight\Auth\InvalidPasswordException $e) {
                    $this->flash->error('Invalid password');
                    RedirectController::redirect('/create_user');
                }
                catch (\Delight\Auth\UserAlreadyExistsException $e) {
                    $this->flash->error('User already exists');
                    RedirectController::redirect('/create_user');
                }

                $dataPost = [];

                if (!empty($_POST['job_title'])) {
                    $dataPost['job_title'] = htmlspecialchars($_POST['job_title']);
                }
                if (!empty($_POST['phone'])) {
                    $dataPost['phone'] = htmlspecialchars($_POST['phone']);
                }
                if (!empty($_POST['address'])) {
                    $dataPost['address'] = htmlspecialchars($_POST['address']);
                }
                if (!empty($_POST['user_status'])) {
                    $dataPost['user_status'] = htmlspecialchars($_POST['user_status']);
                }
                if (!empty($_POST['vk'])) {
                    $dataPost['vk'] = htmlspecialchars($_POST['vk']);
                }
                if (!empty($_POST['instagram'])) {
                    $dataPost['instagram'] = htmlspecialchars($_POST['instagram']);
                }
                if (!empty($_POST['telegram'])) {
                    $dataPost['telegram'] = htmlspecialchars($_POST['telegram']);
                }

                if ($this->userModel->update('users_info', 'user_id', $userId, $dataPost)) {
                    $this->flash->success('Successful data modification!');
                } else {
                    $this->flash->error('Unsuccessful data modification');
                    RedirectController::redirect('/create_user');
                }

                if (isset($_FILES['image'])) {
                    $dataImage['image_id'] = $this->imageModel->addImage($_FILES['image']);// id or false
                    if ($dataImage['image_id'] != false) {
                        $this->userModel->update('users_info', 'user_id', $userId, $dataImage);
                    }
                }
            }
            
            RedirectController::redirect('/users');
        } else {
            RedirectController::redirect('/login');
        }
    }

    public function getPageProfile($data)
    {
        if ($this->auth->isLoggedIn()) {
            $userData['user'] = $this->userModel->getUser($data['id']);
            $this->render->getPage('profile', $userData);
        } else {
            RedirectController::redirect('/login');
        }
    }

    public function getPageEdit($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) { 
                $userData['user'] = $this->userModel->getUser($data['id']);
                $this->render->getPage('edit', $userData);
            } else {
                RedirectController::redirect('/users');  
            }
        }
        
        RedirectController::redirect('/login');
    }

    public function editUser($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) {

                if (!empty($_POST['username'])) {
                    $username['username'] = htmlspecialchars($_POST['username']);
                    if ($this->userModel->update('users', 'id', $data['id'], $username)) {
                        $this->flash->success('Successful username modification!');
                    } else {
                        $this->flash->error('Unsuccessful data modification');
                    }
                }

                $dataPost = [];

                if (!empty($_POST['job_title'])) {
                    $dataPost['job_title'] = htmlspecialchars($_POST['job_title']);
                }
                if (!empty($_POST['phone'])) {
                    $dataPost['phone'] = htmlspecialchars($_POST['phone']);
                }
                if (!empty($_POST['address'])) {
                    $dataPost['address'] = htmlspecialchars($_POST['address']);
                }

                if ($this->userModel->update('users_info', 'user_id', $data['id'], $dataPost)) {
                    $this->flash->success('Successful data modification!');
                } else {
                    $this->flash->error('Unsuccessful data modification');
                }

                RedirectController::redirect("/edit/{$data['id']}");
            }
        }

        RedirectController::redirect('/login');
    }

    public function getPageImage($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) { 
                $userData['user'] = $this->userModel->getUser($data['id']);
                $this->render->getPage('image', $userData);
            } else {
                RedirectController::redirect('/users');
            }
        }

        RedirectController::redirect('/login');
    }

    public function imageUser($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) {
                if (isset($_FILES['image'])) {
                    $dataImage['image_id'] = $this->imageModel->addImage($_FILES['image']);// id or false
                    if ($dataImage['image_id'] != false) {
                        $this->imageModel->deleteImage($data['id']);
                        $this->userModel->update('users_info', 'user_id', $data['id'], $dataImage);
                    }
                }
            }
            RedirectController::redirect("/image/{$data['id']}");
        } else {
            RedirectController::redirect('/login');
        }
    }

    public function getPageStatus($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) {
                $userData['user'] = $this->userModel->getUser($data['id']);
                $this->render->getPage('status', $userData);
            } else {
                RedirectController::redirect('/users');
            }
        }

        RedirectController::redirect('/login');
    }

    public function statusUser($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) {
                $dataPost = [];
                if (!empty($_POST['user_status'])) {
                    $dataPost['user_status'] = htmlspecialchars($_POST['user_status']);
                    if ($this->userModel->update('users_info', 'user_id', $data['id'], $dataPost)) {
                        $this->flash->success('Successful data modification!');
                        RedirectController::redirect("/users");
                    } else {
                        $this->flash->error('Unsuccessful data modification');
                    }
                } else {
                    $this->flash->error('The Status field is empty');
                }

                RedirectController::redirect("/status/{$data['id']}");
            }

            RedirectController::redirect("/users");
        }
        
        RedirectController::redirect('/login');
    }

    public function getPageSecurity($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) {
                $userData['user'] = $this->userModel->getUser($data['id']);
                $this->render->getPage('security', $userData);
            } else {
                RedirectController::redirect('/users');
            }
        }

        RedirectController::redirect('/login');
    }

    public function securityUser($data)
    {
        if ($this->auth->isLoggedIn()) {
            if ($_SESSION['user']->role == \Delight\Auth\Role::ADMIN || $_SESSION['auth_user_id'] == $data['id']) {
                $dataPost = [];
                if (!empty($_POST['email'])) {
                    $dataPost['email'] = htmlspecialchars($_POST['email']);

                    if (filter_var($dataPost['email'], FILTER_VALIDATE_EMAIL)) {
                        $userData = $this->userModel->getUserForEmail($data['id']);

                        if (!$userData) {
                            if ($this->userModel->update('users', 'id', $data['id'], $dataPost)) {

                                if ($_SESSION['auth_user_id'] == $data['id']) {
                                    $_SESSION['auth_email'] = $dataPost['email'];
                                    $_SESSION['user']->email = $dataPost['email'];
                                }

                                $this->flash->success('Successful email modification!');
                            } else {
                                $this->flash->error('Unsuccessful data modification');
                            }
                        } else {
                            $this->flash->error('The email address already exists');
                        }
                    } else {
                        $this->flash->error('The email is unvalide');
                    }
                }

                if (!empty($_POST['password']) && !empty($_POST['password_confirmation'])) {
                    if ((empty($_POST['password']) && !empty($_POST['password_confirmation']))
                    || (!empty($_POST['password']) && empty($_POST['password_confirmation']))) {
                        $this->flash->error('The password or password confirmationis is empty');
                    } else {
                        if (($_POST['password'] == $_POST['password_confirmation'])) {
                            try {
                                $this->auth->admin()->changePasswordForUserById($data['id'], $_POST['password']);
                                $this->flash->success('Successful password modification!');
                            }
                            catch (\Delight\Auth\UnknownIdException $e) {
                                $this->flash->error('The ID is Unknown');
                            }
                            catch (\Delight\Auth\InvalidPasswordException $e) {
                                $this->flash->error('The password is invalid');
                            }
                        } else {
                            $this->flash->error('The password and password confirmationis are not equal');
                        }
                    }
                }
            }

            RedirectController::redirect("/security/{$data['id']}");
        } else {
            RedirectController::redirect('/login');
        }
    }
}
