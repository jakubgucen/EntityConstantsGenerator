<?php

namespace JakubGucen\EntityConstantsGenerator\TestResource\InvalidOneEntity;

/**
 * @ORM\Entity
 */
class Attribute
{
    #region JakubGucen-EntityConstantsGenerator
    use \JakubGucen\EntityConstantsGenerator\Traits\MetaEntityTrait;
    const ID = 'id';
    const ONE_HANDED = 'oneHanded';
    const PLAYER = 'player';
    const PLAYERS = 'players';
    const STRENGTH = 'strength';
    #endregion

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $strength = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $oneHanded = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Player", mappedBy="player", orphanRemoval=true)
     */
    private $players;

    public function __construct()
    {
        $this->players = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStrength(): ?int
    {
        return $this->strength;
    }

    public function setStrength(int $strength): self
    {
        $this->strength = $strength;

        return $this;
    }

    public function getOneHanded(): ?int
    {
        return $this->oneHanded;
    }

    public function setOneHanded(int $oneHanded): self
    {
        $this->oneHanded = $oneHanded;

        return $this;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        $this->players[] = $player;

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        $key = array_search($player, $this->players, true);
        unset($this->players[$key]);

        return $this;
    }
}
