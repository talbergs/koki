<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MartanLV\Koki\Tree;
use MartanLV\Koki\Interval;
use Tests\ExtendedInterval;
use Tests\ImplementedInterval;

/**
 * @covers Tree
 */
final class TreeTest extends TestCase
{

    public function testInterSelectsYields(): void
    {
        $b = [
            new Interval(9, 11), // i=0
            new Interval(5, 15), // <<
            new Interval(10, 20), // <<
            new Interval(30, 40), // <<
            new Interval(40, 50), // <<
            new Interval(60, 70), // <<
            new Interval(65, 70), // <<
            new Interval(71, 72), // i=7
        ];

        $a = (new Tree($b));
        $len = 0;
        foreach ($a->yieldInterSelect(15, 70) as $i) {
            $index = array_search($i, $b);
            $this->assertTrue(!in_array($index, [0, 7]));
            $len ++;
        }

        $this->assertEquals($len, 6);
    }

    public function testInterSelects3(): void
    {
        $b = [
            new Interval(9, 11),
            new Interval(5, 15), // <<
            new Interval(10, 20), // <<
            new Interval(30, 40), // <<
            new Interval(40, 50), // <<
            new Interval(60, 70), // <<
            new Interval(65, 70), // <<
            new Interval(71, 72),
        ];
        $a = (new Tree($b));
        $c = $a->interSelect(15, 70);

        $this->assertEquals("5-15;10-20;30-40;40-50;60-70;65-70;", $this->sig($c));
    }

    public function testInterSelects2(): void
    {
        $b = [
            new Interval(1, 10),
            new Interval(5, 15), // <<
            new Interval(10, 20), // <<
            new Interval(30, 40), // <<
            new Interval(40, 50), // <<
            new Interval(60, 70), // <<
            new Interval(65, 70), // <<
        ];
        $a = (new Tree($b));
        $c = $a->interSelect(15, 70);

        $this->assertEquals("5-15;10-20;30-40;40-50;60-70;65-70;", $this->sig($c));
    }

    public function testInterSelects(): void
    {
        $b = [
            new Interval(1, 10),
            new Interval(5, 15),
            new Interval(10, 20),
            new Interval(30, 40), // <<
            new Interval(40, 50), // <<
            new Interval(60, 70), // <<
            new Interval(65, 70),
        ];
        $a = (new Tree($b));
        $c = $a->interSelect(29, 62);

        $this->assertEquals("30-40;40-50;60-70;", $this->sig($c));
    }

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

    public function testSelectsAll(): void
    {
        $b = [
            new Interval(3, 5),
            new Interval(1, 1),
            new Interval(4, 6),
            new Interval(3, 5),
            new Interval(5, 5),
            new Interval(4, 4),
            new Interval(3, 3),
            new Interval(3, 3),
            new Interval(3, 5),
            new Interval(3, 5),
        ];
        $a = new Tree($b);
        $this->assertEquals(count($a->all()), count($b));
    }

    public function testFlowsEmpty(): void
    {
        $a = new Tree([]);
        foreach ($a->yieldSelect(1, 2) as $i) {
            $this->assertEmpty(1);
        }

        $this->assertEmpty($a->select(2, 3));
    }

    public function testPerformantGenerator(): void
    {
        $a = new Tree([
            new Interval(3, 5),
            new Interval(1, 1),
            new Interval(4, 6),
            new Interval(3, 5),
            new Interval(5, 5),
            new Interval(4, 4),
            new Interval(3, 3),
            new Interval(3, 3),
            new Interval(3, 5),
            new Interval(3, 5),
        ]);
        foreach ($a->yieldSelect(2, 4) as $i) {
            $this->assertEquals($i->getStart(), 3);
            $this->assertEquals($i->getEnd(), 3);
        };
    }

    public function testExtends(): void
    {
        $a = new Tree([
            new ExtendedInterval(3, 5, 231),
            new ExtendedInterval(1, 1, 231),
            new ExtendedInterval(4, 6, 231),
            new ExtendedInterval(3, 5, 231),
        ]);
        foreach ($a->yieldSelect(2, 6) as $i) {
            $this->assertEquals(231, $i->meta);
        }
    }

    public function testImplements(): void
    {
        $a = new Tree([
            new ImplementedInterval(3, 5, 231),
            new ImplementedInterval(1, 1, 231),
            new ImplementedInterval(4, 6, 231),
            new ImplementedInterval(3, 5, 231),
        ]);
        foreach ($a->yieldSelect(2, 6) as $i) {
            $this->assertEquals(231, $i->meta);
        }
    }

    public function testSelectsDuplicates(): void
    {
        $a = new Tree([
            new Interval(3, 5),
            new Interval(1, 1),
            new Interval(4, 6),
            new Interval(3, 5),
        ]);
        $b = $a->select(2, 6);
        $this->assertEquals(2, count($b));
    }

    public function testSelectsAllRange(): void
    {
        $i = [
            new Interval(4, 6),
            new Interval(1, 1),
            new Interval(1, 1),
            new Interval(3, 5),
            new Interval(3, 5),
        ];
        $a = new Tree($i);
        $b = $a->select(0, 10);
        $this->assertEquals(count($i), count($b));
    }
}
