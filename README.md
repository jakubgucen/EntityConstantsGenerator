# Usage
```php
$entityData = new EntityData();
$entityData
    ->setNamespace('App\Entity')
    ->setDir(getcwd() . '/src/Entity');

$generator = new Generator([ $entityData ]);
$generator->run();

// You can rollback the changes with:
// $generator->rollback();
```
