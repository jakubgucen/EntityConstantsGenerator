<?php

namespace Tests\JakubGucen\EntityConstantsGenerator\Helper;

use JakubGucen\EntityConstantsGenerator\Model\EntityData;
use JakubGucen\EntityConstantsGenerator\Model\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    const ENTITY_NAMESPACE = 'TestResource\JakubGucen\EntityConstantsGenerator\Entity';
    const TEST_ENTITIES = [
        'Attribute',
        'Player'
    ];

    protected ?string $projectDir = null;
    protected ?string $testEntityDir = null;

    protected function setUp(): void
    {
        $this->projectDir = getcwd();
        $this->testEntityDir = $this->projectDir . '/test-resource/JakubGucen/EntityConstantsGenerator/Entity';
    }

    protected function tearDown(): void
    {
        $path = $this->getEntityPath('AttributeTmp');
        @unlink($path);

        $path = $this->getEntityPath('PlayerTmp');
        @unlink($path);
    }

    public function testRun()
    {
        $this->loadEntities();

        $fcs = $this->getEntitiesFcs();

        $entityData = new EntityData();
        $entityData
            ->setNamespace(self::ENTITY_NAMESPACE)
            ->setDir($this->testEntityDir);

        $generator = new Generator([ $entityData ]);

        // run then check
        $generator->run();
        $fcsAfterRun = $this->getEntitiesFcs();
        $this->checkEntitiesFcs($fcs, $fcsAfterRun, false);
        $this->checkEntityAttributeAfterRun();
        $this->checkEntityPlayerAfterRun();

        // rollback then check
        $generator->rollback();
        $fcsAfterRollback = $this->getEntitiesFcs();
        $this->checkEntitiesFcs($fcs, $fcsAfterRollback, true);
    }

    protected function getEntityPath(string $name): string
    {
        return $this->testEntityDir . '/' . $name . '.php';
    }

    protected function getEntityFc(string $name): string
    {
        $path = $this->getEntityPath($name);
        return file_get_contents($path);
    }

    protected function getEntitiesFcs(): array
    {
        $fcs = [];
        foreach (self::TEST_ENTITIES as $entityName) {
            $fcs[$entityName] = $this->getEntityFc($entityName);
        }

        return $fcs;
    }

    protected function checkEntitiesFcs(
        array $expectedFcs,
        array $actualFcs,
        bool $expectedSame
    ): void {
        foreach ($expectedFcs as $entityName => $expectedFc) {
            $this->assertSame($expectedSame, $expectedFc === $actualFcs[$entityName]);
        }
    }

    protected function loadEntity(string $path): void
    {
        require $path;
    }

    /**
     * @return string new path
     */
    protected function reloadEntity(string $entityName, string $newEntityName): string
    {
        $fc = $this->getEntityFc($entityName);

        $newPath = $this->getEntityPath($newEntityName);
        $newFc = str_replace(
            [
                "class {$entityName}",
                "interface I{$entityName}",
                " implements I{$entityName}"
            ],
            [
                "class {$newEntityName}",
                "interface I{$newEntityName}",
                " implements I{$newEntityName}"
            ],
            $fc
        );

        file_put_contents($newPath, $newFc);
        $this->loadEntity($newPath);

        return $newPath;
    }

    protected function loadEntities(): void
    {
        foreach (self::TEST_ENTITIES as $entityName) {
            $path = $this->getEntityPath($entityName);
            $this->loadEntity($path);
        }
    }

    protected function checkEntityAttributeAfterRun(): void
    {
        // load the entity
        $newPath = $this->reloadEntity('Attribute', 'AttributeTmp');

        $class = self::ENTITY_NAMESPACE . '\AttributeTmp';
        $attribute = new $class;

        // check constants
        $this->assertSame('id', $class::ID);
        $this->assertSame('strength', $class::STRENGTH);
        $this->assertSame('oneHanded', $class::ONE_HANDED);
        $this->assertSame('player', $class::PLAYER);
        $this->assertSame('players', $class::PLAYERS);

        // check methods
        $playerClass = self::ENTITY_NAMESPACE . '\Player';
        $player = new $playerClass;

        $attribute
            ->set($class::STRENGTH, 20)
            ->set($class::ONE_HANDED, 40)
            ->add($class::PLAYER, $player);

        $this->assertSame($attribute->getStrength(), $attribute->get($class::STRENGTH));
        $this->assertSame(20, $attribute->getStrength());

        $this->assertSame($attribute->getOneHanded(), $attribute->get($class::ONE_HANDED));
        $this->assertSame(40, $attribute->getOneHanded());

        $this->assertSame($attribute->getPlayers(), $attribute->get($class::PLAYERS));
        $this->assertIsArray($attribute->getPlayers());
        $this->assertCount(1, $attribute->getPlayers());
        $this->assertSame($player, $attribute->getPlayers()[0]);

        $attribute->remove($class::PLAYER, $player);
        $this->assertIsArray($attribute->getPlayers());
        $this->assertCount(0, $attribute->getPlayers());

        unlink($newPath);
    }

    protected function checkEntityPlayerAfterRun(): void
    {
        // load the entity
        $newPath = $this->reloadEntity('Player', 'PlayerTmp');

        $class = self::ENTITY_NAMESPACE . '\PlayerTmp';
        $player = new $class;

        // check constants
        $this->assertSame('id', $class::ID);

        unlink($newPath);
    }
}
