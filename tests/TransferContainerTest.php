<?php

namespace Xaerobiont\TransferContainer\Tests;

use PHPUnit\Framework\TestCase;
use Xaerobiont\TransferContainer\ContainerInterface;
use Xaerobiont\TransferContainer\Tests\DTO\AnotherCat;
use Xaerobiont\TransferContainer\Tests\DTO\AnotherTree;
use Xaerobiont\TransferContainer\Tests\DTO\Cat;
use Xaerobiont\TransferContainer\Tests\DTO\Laptop;
use Xaerobiont\TransferContainer\Tests\DTO\Tree;
use Xaerobiont\TransferContainer\Transferable;
use Xaerobiont\TransferContainer\TransferContainer;
use Xaerobiont\TransferContainer\TransferContainerException;

final class TransferContainerTest extends TestCase
{
    /**
     * @test
     */
    public function test_container()
    {
        $container = new TransferContainer();
        self::assertInstanceOf(ContainerInterface::class, $container);

        $laptops = [];
        for ($i = 1; $i <= 3; $i++) {
            $laptops[] = $this->generateDTO(Laptop::class, ['mark' => 'LENOVO']);
        }
        $cat = $this->generateDTO(Cat::class, ['color' => 'white']);
        $trees = [];
        for ($i = 1; $i <= 10; $i++) {
            $trees[] = $this->generateDTO(Tree::class, ['name' => 'sequoia']);
        }

        $container->put($laptops);
        $container->put($cat);
        $container->put($trees);

        $data = $container->pack();

        // sending data through stars and galaxies...

        $receivedCats = 0;
        $receivedTrees = 0;
        $receivedLaptops = 0;
        foreach (TransferContainer::unpack($data) as $item) {
            self::assertInstanceOf(Transferable::class, $item);
            if ($item instanceof Laptop) {
                $receivedLaptops++;
                self::assertEquals('LENOVO', $item->toArray()['mark']);
            } elseif ($item instanceof Tree) {
                $receivedTrees++;
                self::assertEquals('sequoia', $item->toArray()['name']);
            } elseif ($item instanceof Cat) {
                $receivedCats++;
                self::assertEquals('white', $item->toArray()['color']);
            }
        }
        self::assertEquals(1, $receivedCats);
        self::assertEquals(3, $receivedLaptops);
        self::assertEquals(10, $receivedTrees);
    }

    /**
     * @test
     */
    public function test_container_unpack_map()
    {
        $container = new TransferContainer();
        $cat = $this->generateDTO(Cat::class, ['color' => 'black']);
        $tree = $this->generateDTO(Tree::class, ['name' => 'cedar']);
        $container->put([$cat, $tree]);
        $data = $container->pack();

        $unpackMap = [
            Cat::class => AnotherCat::class,
            Tree::class => AnotherTree::class,
        ];
        foreach (TransferContainer::unpack($data, $unpackMap) as $item) {
            self::assertTrue($item instanceof AnotherTree || $item instanceof AnotherCat);
        }
    }

    /**
     * @test
     */
    public function test_container_multibyte()
    {
        $cat = $this->generateDTO(Cat::class, ['color' => '白い']);
        $cat2 = $this->generateDTO(Cat::class, ['color' => 'белый']);
        $cat3 = $this->generateDTO(Cat::class, ['color' => 'أبيض']);

        $container = new TransferContainer();
        $container->put([$cat, $cat2, $cat3]);
        $data = $container->pack();

        foreach (TransferContainer::unpack($data) as $cat) {
            self::assertTrue(in_array($cat->getColor(), ['白い', 'белый', 'أبيض']));
        }
    }

    private function generateDTO(string $class, array $params = []): Transferable
    {
        $dto = new $class;
        $dto->fromArray($params);

        return $dto;
    }
}