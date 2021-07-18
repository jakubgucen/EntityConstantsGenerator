<?php

namespace Tests\JakubGucen\EntityConstantsGenerator\Helper;

use JakubGucen\EntityConstantsGenerator\Exception\EntityFileException;
use JakubGucen\EntityConstantsGenerator\Model\EntitiesData;
use JakubGucen\EntityConstantsGenerator\Model\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    private ?string $projectDir = null;

    protected function setUp(): void
    {
        $this->projectDir = getcwd();
    }

    protected function tearDown(): void
    {
        $entityDir = $this->projectDir . '/test-resource/JakubGucen/EntityConstantsGenerator/Entity';

        $path = $this->getEntityPath($entityDir, 'AttributeTmp');
        @unlink($path);

        $path = $this->getEntityPath($entityDir, 'PlayerTmp');
        @unlink($path);
    }

    public function testRunEntity()
    {
        $entityDir = $this->projectDir . '/test-resource/JakubGucen/EntityConstantsGenerator/Entity';
        $entityNamespace = 'TestResource\JakubGucen\EntityConstantsGenerator\Entity';
        $entityNames = [
            'Attribute',
            'Player'
        ];

        $this->loadEntities($entityDir, $entityNames);
        $fcs = $this->getEntitiesFcs($entityDir, $entityNames);

        $entitiesData = new EntitiesData();
        $entitiesData
            ->setNamespace($entityNamespace)
            ->setDir($entityDir);

        $generator = new Generator($entitiesData);

        // run then check
        $generator->run();
        $fcsAfterRun = $this->getEntitiesFcs($entityDir, $entityNames);
        $this->checkEntitiesFcs($fcs, $fcsAfterRun, false);
        $this->checkEntityAttributeAfterRun($entityDir, $entityNamespace);
        $this->checkEntityPlayerAfterRun($entityDir, $entityNamespace);

        // rollback then check
        $generator->rollback();
        $fcsAfterRollback = $this->getEntitiesFcs($entityDir, $entityNames);
        $this->checkEntitiesFcs($fcs, $fcsAfterRollback, true);
    }

    public function testRunRegionEntity()
    {
        $entityDir = $this->projectDir . '/test-resource/JakubGucen/EntityConstantsGenerator/RegionEntity';
        $entityNamespace = 'TestResource\JakubGucen\EntityConstantsGenerator\RegionEntity';
        $entityNames = [
            'Attribute',
            'Player',
        ];

        $this->loadEntities($entityDir, $entityNames);
        $fcs = $this->getEntitiesFcs($entityDir, $entityNames);

        $entitiesData = new EntitiesData();
        $entitiesData
            ->setNamespace($entityNamespace)
            ->setDir($entityDir);

        $generator = new Generator($entitiesData);

        // run then check
        $generator->run();
        $fcsAfterRun = $this->getEntitiesFcs($entityDir, $entityNames);
        $this->checkEntitiesFcs($fcs, $fcsAfterRun, true);
    }

    public function testRunInvalidEntity(): void
    {
        $entityDir = $this->projectDir . '/test-resource/JakubGucen/EntityConstantsGenerator/InvalidEntity';
        $entityNamespace = 'TestResource\JakubGucen\EntityConstantsGenerator\InvalidEntity';
        $entityNames = [
            'Player',
        ];

        $this->runForInvalidEntity(
            $entityDir,
            $entityNamespace,
            $entityNames,
            'Could not find expression'
        );
    }

    public function testRunInvalidRegionEntity(): void
    {
        $entityDir = $this->projectDir . '/test-resource/JakubGucen/EntityConstantsGenerator/InvalidRegionEntity';
        $entityNamespace = 'TestResource\JakubGucen\EntityConstantsGenerator\InvalidRegionEntity';
        $entityNames = [
            'Player',
        ];

        $this->runForInvalidEntity(
            $entityDir,
            $entityNamespace,
            $entityNames,
            'Could not find end of region in'
        );
    }

    public function testRunInvalidOneEntity(): void
    {
        $entityDir = $this->projectDir . '/test-resource/JakubGucen/EntityConstantsGenerator/InvalidOneEntity';
        $entityNamespace = 'TestResource\JakubGucen\EntityConstantsGenerator\InvalidOneEntity';
        $entityNames = [
            'Attribute',
            'Player',
            'PlayerInvalid',
        ];

        $this->runForInvalidEntity(
            $entityDir,
            $entityNamespace,
            $entityNames,
            'Could not find expression'
        );
    }

    private function runForInvalidEntity(
        string $entityDir,
        string $entityNamespace,
        array $entityNames,
        string $expectedExceptionMessage
    ): void {
        $this->loadEntities($entityDir, $entityNames);
        $fcs = $this->getEntitiesFcs($entityDir, $entityNames);

        $entitiesData = new EntitiesData();
        $entitiesData
            ->setNamespace($entityNamespace)
            ->setDir($entityDir);

        $generator = new Generator($entitiesData);
        try {
            $generator->run();
        } catch (EntityFileException $e) {}

        $this->assertTrue(isset($e));
        $this->assertInstanceOf(EntityFileException::class, $e);
        $this->assertStringContainsString($expectedExceptionMessage, $e->getMessage());

        $fcsAfterRun = $this->getEntitiesFcs($entityDir, $entityNames);
        $this->checkEntitiesFcs($fcs, $fcsAfterRun, true);
    }

    private function getEntityPath(string $entityDir, string $name): string
    {
        return $entityDir . '/' . $name . '.php';
    }

    private function getEntityFc(string $entityDir, string $name): string
    {
        $path = $this->getEntityPath($entityDir, $name);
        $fileContent = file_get_contents($path);
        $this->assertIsString($fileContent);

        return $fileContent;
    }

    private function getEntitiesFcs(string $entityDir, array $entityNames): array
    {
        $fcs = [];
        foreach ($entityNames as $entityName) {
            $fcs[$entityName] = $this->getEntityFc($entityDir, $entityName);
        }

        return $fcs;
    }

    private function checkEntitiesFcs(
        array $expectedFcs,
        array $actualFcs,
        bool $expectedSame
    ): void {
        foreach ($expectedFcs as $entityName => $expectedFc) {
            $this->assertSame($expectedSame, $expectedFc === $actualFcs[$entityName]);
        }
    }

    private function loadEntity(string $path): void
    {
        require $path;
    }

    /**
     * @return string new path
     */
    private function reloadEntity(
        string $entityDir,
        string $entityName,
        string $newEntityName
    ): string {
        $fc = $this->getEntityFc($entityDir, $entityName);

        $newPath = $this->getEntityPath($entityDir, $newEntityName);
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

    private function loadEntities(string $entityDir, array $entityNames): void
    {
        foreach ($entityNames as $entityName) {
            $path = $this->getEntityPath($entityDir, $entityName);
            $this->loadEntity($path);
        }
    }

    private function checkEntityAttributeAfterRun(string $entityDir, string $entityNamespace): void
    {
        // load the entity
        $newPath = $this->reloadEntity($entityDir, 'Attribute', 'AttributeTmp');

        $class = $entityNamespace . '\AttributeTmp';
        $attribute = new $class;

        // check constants
        $this->assertSame('id', $class::ID);
        $this->assertSame('strength', $class::STRENGTH);
        $this->assertSame('oneHanded', $class::ONE_HANDED);
        $this->assertSame('player', $class::PLAYER);
        $this->assertSame('players', $class::PLAYERS);

        // check methods
        $playerClass = $entityNamespace . '\Player';
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

    private function checkEntityPlayerAfterRun(string $entityDir, string $entityNamespace): void
    {
        // load the entity
        $newPath = $this->reloadEntity($entityDir, 'Player', 'PlayerTmp');

        $class = $entityNamespace . '\PlayerTmp';
        $player = new $class;

        // check constants
        $this->assertSame('id', $class::ID);

        unlink($newPath);
    }
}
