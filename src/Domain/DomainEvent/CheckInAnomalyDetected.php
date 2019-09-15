<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class CheckInAnomalyDetected extends AggregateChanged
{
    public static function inBuilding(string $username, Uuid $building) : self
    {
        return self::occur($building->toString(), ['username' => $username]);
    }

    public function username() : string
    {
        return $this->payload['username'];
    }

    public function building()
    {
        return Uuid::fromString($this->aggregateId());
    }
}
