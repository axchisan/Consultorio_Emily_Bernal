<?php
define('host', getenv('MYSQL_HOST') ?: 'localhost');
define('user', getenv('MYSQL_USER') ?: 'root');
define('password', getenv('MYSQL_PASSWORD') ?: '');
define('database', getenv('MYSQL_DATABASE') ?: 'perfect_teeth');
define('port', getenv('MYSQL_PORT') ?: 3306);
?>