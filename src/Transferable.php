<?php

declare(strict_types=1);

namespace Xaerobiont\TransferContainer;

interface Transferable
{
    /**
     * Represent object as an array
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Populate data from array
     *
     * @param array $data
     *
     * @return void
     */
    public function fromArray(array $data): void;
}