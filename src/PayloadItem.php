<?php

declare(strict_types=1);

namespace Xaerobiont\TransferContainer;

use JsonSerializable;

final readonly class PayloadItem implements JsonSerializable
{
    function __construct(private string $class, private array $data)
    {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}