<?php

declare(strict_types=1);

namespace Xaerobiont\TransferContainer;

use Iterator;
use Throwable;

class TransferContainer implements ContainerInterface
{
    /**
     * @var PayloadItem[]
     */
    protected array $payload = [];

    public function pack(): string
    {
        return gzdeflate(json_encode($this->payload), 9);
    }

    /**
     * @param string $packed
     * @param array<string, string> $map
     * @param bool $skipInvalid
     *
     * @return Iterator
     * @throws TransferContainerException
     */
    public static function unpack(string $packed, array $map = [], bool $skipInvalid = false): Iterator
    {
        $raw = json_decode(gzinflate($packed), true);
        if (!is_array($raw)) {
            throw new TransferContainerException('Unpack failed: Invalid pack provided', 0);
        }

        foreach ($raw as $item) {
            try {
                if (!is_array($item)) {
                    throw new TransferContainerException('Invalid pack item provided');
                }
                /** @psalm-suppress MixedArgument */
                $payloadItem = new PayloadItem($item['class'], $item['data']);
                $className = array_key_exists($payloadItem->getClass(), $map) ?
                    $map[$payloadItem->getClass()] :
                    $payloadItem->getClass();
                if (!class_exists($className)) {
                    throw new TransferContainerException(
                        "Received class $className does not exist and should be added to map"
                    );
                }
                /** @psalm-suppress MixedMethodCall */
                $object = new $className();
                if (!($object instanceof Transferable)) {
                    throw new TransferContainerException(
                        'Transfer container object must implement Transferable interface'
                    );
                }
                $object->fromArray($payloadItem->getData());

                yield $object;
            } catch (Throwable $e) {
                if (!$skipInvalid) {
                    throw new TransferContainerException("Unpack failed: {$e->getMessage()}", 0, $e);
                }
            }
        }
    }

    /**
     * @param Transferable|Transferable[] $objects
     *
     * @return void
     * @throws TransferContainerException
     */
    public function put(Transferable|array $objects): void
    {
        if (!is_array($objects)) {
            $objects = [$objects];
        }
        foreach ($objects as $object) {
            if (!($object instanceof Transferable)) {
                throw new TransferContainerException(
                    'Transfer container object must implement Transferable interface'
                );
            }
            $this->payload[] = new PayloadItem($object::class, $object->toArray());
        }
    }

    public function clear(): void
    {
        $this->payload = [];
    }
}