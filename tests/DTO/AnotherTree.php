<?php

namespace Xaerobiont\TransferContainer\Tests\DTO;

use Xaerobiont\TransferContainer\Transferable;

class AnotherTree implements Transferable
{
    protected ?string $name = null;

    public function fromArray(array $data): void
    {
        $this->name = $data['name'];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}