<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/database/connection.php';

putenv('RAY_HOST=host.docker.internal');
putenv('RAY_PORT=23517');


die('terminated script');
