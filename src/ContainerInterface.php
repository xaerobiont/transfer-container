<?php

declare(strict_types=1);

namespace Xaerobiont\TransferContainer;

use Iterator;
use JsonSerializable;

interface ContainerInterface extends JsonSerializable
{
    /**
     * Put object or array of objects to container payload
     *
     * @param Transferable|Transferable[] $objects
     *
     * @return void
     */
    public function put(Transferable|array $objects): void;

    /**
     * Pack data to send
     *
     * @return string
     */
    public function pack(): string;

    /**
     * Unpack data
     *
     * @param string $packed
     * @param array<string, string> $map key => value array which define how to populate received payload. 'x\y\z' => 'w\d\s'
     * @param bool $skipInvalid
     *
     * @return Iterator
     */
    public static function unpack(string $packed, array $map, bool $skipInvalid): Iterator;

    /**
     * Drops all payload
     *
     * @return void
     */
    public function clear(): void;
}