<?php

    return [
        // database configuration
        'db_host' => 'localhost',
        'db_user' => "root",
        'db_password' => "password",
        'db_database' => "database",

        // the table name
        'db_table' => "data_keystore",

        // the secret key
        'key_secret' => "ThisISTheSuperSecretKey",
        
        // salt used when hashing
        'salt' => "examplesalt",

        // the data name separator
        'data_name_separator' => ":",
    ]
?>