<?php

declare(strict_types=1);

use MartanLV\Koki\Interval;
use MartanLV\Koki\Tree;
use PHPUnit\Framework\TestCase;

/**
 * @covers Tree
 */
final class TreeUpdatesTest extends TestCase
{
    public function sig(array $c): string
    {
        $sig = '';
        usort($c, function ($a, $b) {
            return $a <=> $b;
        });
        array_map(function ($i) use (&$sig) {
            $sig .= "{$i->getStart()}-{$i->getEnd()};";
        }, $c);

        return $sig;
    }

    public function __testRemoveRegion(): void
    {
        $t = new Tree([
        new Interval(1, 2),
        new Interval(2, 4),
        new Interval(4, 10),
        new Interval(10, 20),
        new Interval(20, 30),
        ]);
        $t->removeRegion(6, 16);
        $this->assertEquals('1-2;2-4;20-30;', $this->sig($t->all()));
    }

    public function testAddOneToEmpty(): void
    {
        $t = new Tree();
        $t->add(new Interval(1, 2));
        $this->assertEquals('1-2;', $this->sig($t->all()));
    }

    public function testAddToEmpty(): void
    {
        $t = new Tree();
        $t->add(new Interval(1, 2));
        $t->add(new Interval(2, 3));
        $this->assertEquals('1-2;2-3;', $this->sig($t->all()));
    }

    public function testAddToNonEmpty(): void
    {
        $b = [
            new Interval(1, 8),
            new Interval(9, 10),
            new Interval(10, 20),
            new Interval(20, 22),
            new Interval(23, 28),
            new Interval(24, 27),
        ];
        $a = (new Tree($b));
        $a->add(new Interval(2, 5));
        $a->add(new Interval(25, 30));

        $b[] = new Interval(25, 30);
        $b[] = new Interval(2, 5);
        $c = (new Tree($b));

        $this->assertEquals($this->sig($a->all()), $this->sig($c->all()));
    }
}
