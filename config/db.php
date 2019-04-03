<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => $local_config['db.dsn'],
    'username' => $local_config['db.username'],
    'password' => $local_config['db.password'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
