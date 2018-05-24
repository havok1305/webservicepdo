<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
    ],
//    'secretkey'=>'698ee85b52b7b65dde71e42705f3aa3aa276b173',
    'secretkey'=>getenv('SECRETKEY'),
    'issuer'=>'Life Sistemas'
];