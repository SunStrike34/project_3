<?php

namespace App\Services;

class Config
{
    public static function get($path = null) 
    {
        if ($path) {
            $config = [
                'mysql' => [
                    'host' => 'localhost',
                    'username' => 'root',
                    'password' => '',
                    'database' => 'project_3'
                ]
            ];

            $path = explode('.', $path);

            foreach ($path as $item) {
                if (isset($config[$item])) {
                    $config = $config[$item];
                }
            }

            return $config;
        }

        return false;
    }
}

