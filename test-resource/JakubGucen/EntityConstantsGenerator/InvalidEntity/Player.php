<?php

namespace TestResource\JakubGucen\EntityConstantsGenerator\InvalidEntity;

/**
 * @ORM\Entity
 */
class  Player
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
