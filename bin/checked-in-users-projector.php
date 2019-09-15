#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Building\App;

use Building\Domain\Aggregate\Building;
use Building\Domain\Command;
use Interop\Container\ContainerInterface;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream\StreamName;
use Prooph\ServiceBus\CommandBus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rhumsaa\Uuid\Uuid;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Zend\Expressive\Application;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\WhoopsErrorHandler;

call_user_func(function () {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $sm = require __DIR__ . '/../container.php';

    /** @var $sm ContainerInterface */
    $eventStore = $sm->get(EventStore::class);

    $history = $eventStore->loadEventsByMetadataFrom(
        new StreamName('event_stream'),
        [
            'aggregate_type' => Building::class,
        ]
    );

    foreach ($history as $domainEvent) {
        // ...
    }

    // file_put_contents('../public/building-<UUID>.json', json_encode($users));
});
