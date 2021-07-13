# Usage:
```php
$entitiesData = new EntitiesData();
$entitiesData
    ->setNamespace('App\Entity')
    ->setDir(getcwd() . '/src/Entity');

$generator = new Generator($entitiesData);
$generator->run();

// You can rollback the changes with:
// $generator->rollback();
```


# Sample result:
```php
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

    private $id;
    private $strength = 0;
    private $oneHanded = 0;
    private $players;

    public function getId(): ?int
    // ...

    public function getStrength(): ?int
    // ...

    public function setStrength(int $strength): self
    // ...

    public function getOneHanded(): ?int
    // ...

    public function setOneHanded(int $oneHanded): self
    // ...

    public function getPlayers(): array
    // ...

    public function addPlayer(Player $player): self
    // ...

    public function removePlayer(Player $player): self
    // ...
}
```


# New methods in each entity:
```php
public function get(string $propertyName)
public function set(string $propertyName, $value): self
public function add(string $propertyName, $value): self
public function remove(string $propertyName, $value): self
```