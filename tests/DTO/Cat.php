<?php

namespace Xaerobiont\TransferContainer\Tests\DTO;

use Xaerobiont\TransferContainer\Transferable;

class Cat implements Transferable
{
    protected ?string $color = null;

    public function getColor(): string
    {
        return $this->color;
    }

    public function fromArray(array $data): void
    {
        $this->color = $data['color'];
    }

    public function toArray(): array
    {
        return [
            'color' => $this->color,
        ];
    }
}