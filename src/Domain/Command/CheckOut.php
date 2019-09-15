<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

final class CheckOut extends Command
{
    /** @var Uuid */
    private $building;

    /** @var string */
    private $username;

    private function __construct(Uuid $buildingId, string $username)
    {
        $this->init();

        $this->building = $buildingId;
        $this->username = $username;
    }

    public static function ofBuilding(Uuid $building, string $username) : self
    {
        return new self($building, $username);
    }

    public function username() : string
    {
        return $this->username;
    }

    public function building() : Uuid
    {
        return $this->building;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'username' => $this->username,
            'building' => $this->building->toString(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->username = $payload['username'];
        $this->building = Uuid::fromString($payload['building']);
    }
}
