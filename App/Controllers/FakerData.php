<?php
session_start();
require '../../vendor/autoload.php';

use Faker;
use Delight\Auth\Auth;
use App\Models\UserModel;
use App\Services\Config;
use Aura\SqlQuery\QueryFactory;

function fakerData()
{
    $db = new PDO("mysql:host=" . Config::get('mysql.host'). "; dbname=" . Config::get('mysql.database'), Config::get('mysql.username'), Config::get('mysql.password'));
    $auth = new Auth($db);
    $faker = Faker\Factory::create();
    $queryFactory = new QueryFactory('mysql');
    $user = new UserModel($db, $queryFactory);
    // admin => 1
    // user => 16
    $data1 = [
        'password' => $faker->password(6),
        'username' => $faker->name(),
        'email' => $faker->email()
        
    ];  

    try {
        $iserId = $auth->register($data1['email'], $data1['password'], $data1['username']);
        $data2 = [
            'id' => $iserId,
            'role' => 16,
            'birthplace' => $faker->city() . ' ' . $faker->country(),
            'phone' =>$faker->phoneNumber(),
            'address' =>$faker->address(),
            'job_title' =>$faker->jobTitle(),
            'vk' => $faker->domainName(),
            'instagram' => $faker->domainName(),
            'telegram' => $faker->domainName()
        ];  
        $user->insert("users_info", $data2);
        $_SESSION='';
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
}



for ($i=0; $i < 5; $i++) { 
    fakerData();
}

