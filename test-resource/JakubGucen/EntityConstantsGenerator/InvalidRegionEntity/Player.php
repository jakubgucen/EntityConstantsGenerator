<?php

namespace TestResource\JakubGucen\EntityConstantsGenerator\InvalidRegionEntity;

/**
 * @ORM\Entity
 */
class Player
{
    #region JakubGucen-EntityConstantsGenerator
    use \JakubGucen\EntityConstantsGenerator\Traits\MetaEntityTrait;
    const ID = 'id';

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
