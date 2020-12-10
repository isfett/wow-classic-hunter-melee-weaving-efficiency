<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Pet\Hunter;

/**
 * Class Pet
 */
abstract class Pet
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $id;

    /**
     * Hunter constructor.
     *
     * @param int    $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
