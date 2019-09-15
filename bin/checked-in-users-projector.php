#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Building\App;

call_user_func(function () {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    (require __DIR__ . '/../container.php')
        ->get('project-registered-users-to-public')();
});
