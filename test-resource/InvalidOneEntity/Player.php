<?php

namespace JakubGucen\EntityConstantsGenerator\TestResource\InvalidOneEntity;

interface IPlayer
{
    const ID = 'id';
}

/**
 * @ORM\Entity
 */
class Player implements IPlayer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
