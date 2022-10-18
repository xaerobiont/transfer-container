<?php

declare(strict_types=1);

namespace Xaerobiont\TransferContainer;

use Throwable;

class TransferContainer implements ContainerInterface
{
    /**
     * @var Transferable[]
     */
    protected array $payload = [];
    protected int $iteratorPosition = 0;

    public function pack(): string
    {
        return gzdeflate(json_encode($this), 9);
    }

    /**
     * @param string $packed
     * @param array<string, string> $map
     * @param bool $skipInvalid
     *
     * @return void
     * @throws TransferContainerException
     */
    public function unpack(string $packed, array $map = [], bool $skipInvalid = false): void
    {
        $raw = json_decode(gzinflate($packed), true);
        if (!is_array($raw)) {
            throw new TransferContainerException('Unpack failed: Invalid pack provided', 0);
        }
        foreach ($raw as $item) {
            try {
                if (
                    !is_array($item) || empty($item['class']) || empty($item['data']) ||
                    !is_string($item['class']) || !is_array($item['data'])
                ) {
                    throw new TransferContainerException('Invalid pack item provided');
                }
                $className = array_key_exists($item['class'], $map) ? $map[$item['class']] : $item['class'];
                if (!class_exists($className)) {
                    throw new TransferContainerException(
                        "Received class $className does not exist and should be added to map"
                    );
                }
                /**
                 * @psalm-suppress MixedMethodCall
                 */
                $object = new $className();
                if (!($object instanceof Transferable)) {
                    throw new TransferContainerException(
                        'Transfer container object must implement Transferable interface'
                    );
                }
                $object->fromArray($item['data']);
                $this->put($object);
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
            $this->payload[] = $objects;

            return;
        }
        foreach ($objects as $object) {
            if (!($object instanceof Transferable)) {
                throw new TransferContainerException(
                    'Transfer container object must implement Transferable interface'
                );
            }
            $this->payload[] = $object;
        }
    }

    /**
     * @return Transferable[]
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function jsonSerialize(): mixed
    {
        $payload = [];
        foreach ($this->payload as $item) {
            $payload[] = [
                'class' => $item::class,
                'data' => $item->toArray(),
            ];
        }

        return $payload;
    }

    public function valid(): bool
    {
        return isset($this->payload[$this->iteratorPosition]);
    }

    public function current(): Transferable
    {
        return $this->payload[$this->iteratorPosition];
    }

    public function rewind(): void
    {
        $this->iteratorPosition = 0;
    }

    public function key(): int
    {
        return $this->iteratorPosition;
    }

    public function next(): void
    {
        ++$this->iteratorPosition;
    }
}