<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\CheckInAnomalyDetected;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIn;
use Building\Domain\DomainEvent\UserCheckedOut;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /** @var array<string, null> */
    private $checkedInUsers = [];

    public static function new(string $name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        $anomaly = \array_key_exists($username, $this->checkedInUsers);

        $this->recordThat(UserCheckedIn::fromUserAndBuilding($username, $this->uuid));

        if ($anomaly) {
            $this->recordThat(CheckInAnomalyDetected::inBuilding($username, $this->uuid));
        }
    }

    public function checkOutUser(string $username)
    {
        $anomaly = ! \array_key_exists($username, $this->checkedInUsers);

        $this->recordThat(UserCheckedOut::fromUserAndBuilding($username, $this->uuid));

        if ($anomaly) {
            $this->recordThat(CheckInAnomalyDetected::inBuilding($username, $this->uuid));
        }
    }

    protected function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = Uuid::fromString($event->aggregateId());
        $this->name = $event->name();
    }

    protected function whenUserCheckedIn(UserCheckedIn $event)
    {
        $this->checkedInUsers[$event->username()] = null;
    }

    protected function whenUserCheckedOut(UserCheckedOut $event)
    {
        unset($this->checkedInUsers[$event->username()]);
    }

    protected function whenCheckInAnomalyDetected(CheckInAnomalyDetected $event)
    {
        // Empty (on purpose)
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }
}
