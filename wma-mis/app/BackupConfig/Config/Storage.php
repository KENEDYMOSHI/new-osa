<?php

return [
    'local' => [
        'type' => 'Local',
        'root' => WRITEPATH.'Backups',
    ],
    
    'ftp' => [
        'type' => 'Ftp',
        'host' => '',
        'username' => '',
        'password' => '',
        'root' => '',
        'port' => 21,
        'passive' => true,
        'ssl' => true,
        'timeout' => 30,
    ],
    'sftp' => [
        'type' => 'Sftp',
        'host' => '',
        'username' => '',
        'password' => '',
        'root' => '',
        'port' => 21,
        'timeout' => 10,
        'privateKey' => '',
    ],
    
];