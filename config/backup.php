<?php

 return [
    'bingoforcash'=> [
        'name' => 'bingoforcash',
        'host' => env('BINGO_DB_HOST'),
        'user' => env('BINGO_DB_USER'),
        'password' => env('BINGO_DB_PASSWORD'),
        'database' => '',
        'table' =>[

        ],
        'dir' => 'bingoforcash/db/',
    ],
    'solitairearena'=> [
        'name' => 'solitairearena',
        'host' => env('SOLITAIRE_DB_HOST'),
        'user' => env('SOLITAIRE_DB_USER'),
        'password' => env('SOLITAIRE_DB_PASSWORD'),
        'database' => '',
        'table' =>[

        ],
        'dir' => 'solitairearena/db/',
    ],
    'test'=> [
        'name' => 'test',
        'host' => env('TEST_DB_HOST'),
        'user' => env('TEST_DB_USER'),
        'password' => env('TEST_DB_PASSWORD'),
        'database' => 'zxbingo',
        'table' => ['devices', 'users'],
        'dir' => 'test/db/',
    ],
    // 'database' => [

    // ],

 ]




?>