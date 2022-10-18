<?php

namespace Xaerobiont\TransferContainer\Tests\DTO;

use Xaerobiont\TransferContainer\Transferable;

class Laptop implements Transferable
{
    protected ?string $mark = null;

    public function fromArray(array $data): void
    {
        $this->mark = $data['mark'];
    }

    public function toArray(): array
    {
        return [
            'mark' => $this->mark,
        ];
    }
}