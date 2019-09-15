<?php

declare(strict_types=1);

namespace Specification;

use Assert\Assertion;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Building\Domain\Aggregate\Building;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIn;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Aggregate\AggregateType;
use Rhumsaa\Uuid\Uuid;

final class CheckInCheckOut implements Context
{
    /** @var Building|null */
    private $building;

    /** @var AggregateChanged[] */
    private $history = [];

    /** @var null|AggregateChanged[] */
    private $recordedEvents = null;

    /** @Given /^a building$/ */
    public function aBuilding() : void
    {
        $this->history[] = NewBuildingWasRegistered::occur(
            Uuid::uuid4()->toString(),
            ['name' => 'The name of the building']
        );
    }

    /** @When /^"([^"]+)" checks into the building$/ */
    public function userChecksIntoTheBuilding(string $username) : void
    {
        $this->building()
            ->checkInUser($username);
    }

    /** @Then /^"([^"]*)" should have been checked into the building$/ */
    public function userShouldHaveBeenCheckedIntoTheBuilding(string $username)
    {
        /** @var UserCheckedIn $checkedIn */
        $checkedIn = $this->nextRecordedEvent();

        Assertion::isInstanceOf($checkedIn, UserCheckedIn::class);
        Assertion::eq($checkedIn->username(), $username);
    }

    private function building() : Building
    {
        return $this->building
            ?? $this->building = (new AggregateTranslator())
                ->reconstituteAggregateFromHistory(
                    AggregateType::fromAggregateRootClass(Building::class),
                    new \ArrayIterator($this->history)
                );
    }

    private function nextRecordedEvent() : AggregateChanged
    {
        if (null !== $this->recordedEvents) {
            return \array_shift($this->recordedEvents);
        }

        $this->recordedEvents = (new AggregateTranslator())
            ->extractPendingStreamEvents($this->building());

        return \array_shift($this->recordedEvents);
    }
}
