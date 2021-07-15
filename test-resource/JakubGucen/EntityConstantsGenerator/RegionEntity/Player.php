<?php

namespace TestResource\JakubGucen\EntityConstantsGenerator\RegionEntity;

interface IPlayer
{
    const ID = 'id';
}

/**
 * @ORM\Entity
 */
class Player implements IPlayer
{
    #region JakubGucen-EntityConstantsGenerator
    use \JakubGucen\EntityConstantsGenerator\Traits\MetaEntityTrait;
    #endregion

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
