# Transfer Container

PHP container for transferring DTO between services

[![Latest Stable Version](https://poser.pugx.org/xaerobiont/transfer-container/v/stable.png)](https://packagist.org/packages/xaerobiont/transfer-container)
[![Total Downloads](https://poser.pugx.org/xaerobiont/transfer-container/downloads.png)](https://packagist.org/packages/xaerobiont/transfer-container)

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
    // $item is MyDTO/OtherDTO/ThemDTO object
}

$container->clear();
```