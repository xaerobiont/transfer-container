# Transfer Container

PHP container for transferring DTO between services

## Goals

- Allows to transfer different DTOs into single package
- Provides mapping mechanism. I.e. when sender and receiver has different DTO namespace or even classes
- Compress data packs
- Very simple, lightweight and vendor-independent

## Installation

```json
{
  "require": {
    "xaerobiont/transfer-container": "^1"
  }
}
```

## Usage

For more detailed usage examples see /tests

```php
use Xaerobiont\TransferContainer\Transferable;
use Xaerobiont\TransferContainer\TransferContainer;

class MyDTO implements Transferable {}
class YourDTO implements Transferable {}
class ThemDTO implements Transferable {}

$package = [];
for ($i = 1; $i <= 100; $i++) {
    $package[] = new MyDTO();
    $package[] = new YourDTO();
    $package[] = new ThemDTO();
}

$container = new TransferContainer();
$container->put($package);

$transfer = $container->pack();

// receiver side
$container = new TransferContainer();
$container->unpack($transfer, [
    YourDTO::class => OtherDTO::class
]);

foreach ($container as $item) {
    // $item is MyDTO/YourDTO/ThemDTO object
}
```