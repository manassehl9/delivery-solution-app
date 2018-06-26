<?php

$config = [
    // 'tempdir' => '/home/deploy/apps/saddlejit/current/cronjobs/temp/',
    // 'autoloadpath' => '/home/deploy/apps/saddlejit/current/vendor/autoload.php',
    'tempdir' => '/home/deploy/apps/saddlejit/current/cronjobs/temp/',
    'autoloadpath' => '/home/deploy/apps/saddlejit/current/vendor/autoload.php',
    "db" => [
        "hostname" => "localhost",
	"user" => "jitsaddleuser",
        "password" => "j!tU53r",
        "database" => "jitsaddle",
        "port" => 3306
    ],
    "smtp" => [
        'server' => 'ssl://smtp.gmail.com',
        'user' => 'saddle@netplusadvisory.com',
        'password' => 'Saddle7890',
        'from' => ['saddle@netplusadvisory.com' => 'Saddle Bot'],
        'to' => ['dapo@webmallng.com','adeshina@webmallng.com','emmanuel@netplusadvisory.com'],
        'port' => 465
    ]
]
?>
