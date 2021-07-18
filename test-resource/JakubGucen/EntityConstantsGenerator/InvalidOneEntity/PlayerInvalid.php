<?php

namespace TestResource\JakubGucen\EntityConstantsGenerator\InvalidOneEntity;

/**
 * @ORM\Entity
 */
class  PlayerInvalid
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
