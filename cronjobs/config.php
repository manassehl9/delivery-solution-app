<?php

$config = [
    // 'tempdir' => '/home/deploy/apps/saddlejit/current/cronjobs/temp/',
    // 'autoloadpath' => '/home/deploy/apps/saddlejit/current/vendor/autoload.php',
    'tempdir' => '/home/deploy/apps/saddlejit/current/cronjobs/temp/',
    'autoloadpath' => '/home/deploy/apps/saddlejit/current/vendor/autoload.php',
    "db" => [
        "hostname" => "localhost",
	    "user" => "jituser",
        "password" => "",
        "database" => "",
        "port" => 3306
    ],
    "smtp" => [
        'server' => 'ssl://smtp.gmail.com',
        'user' => '',
        'password' => '',
        'from' => ['' => 'Saddle Bot'],
        'to' => ['','adeshina@webmallng.com',''],
        'port' => 465
    ]
]
?>
