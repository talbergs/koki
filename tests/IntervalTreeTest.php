<?php

declare(strict_types=1);

use MartanLV\Koki\Interval;
use MartanLV\Koki\Tree;
use PHPUnit\Framework\TestCase;
use Tests\ExtendedInterval;
use Tests\ImplementedInterval;

/**
 * @covers Tree
 */
final class TreeTest extends TestCase
{
    public function makeTreeProvider($i)
    {
        $datas = [];

        foreach ($this->randomBuildTree($i) as $tree) {
            $datas[] = [$tree];
        }

        return $datas;
    }

    public function providerTestInterSelectsYields()
    {
        return $this->makeTreeProvider([
            new Interval(9, 11), // i=0
            new Interval(5, 15), // <<
            new Interval(10, 20), // <<
            new Interval(30, 40), // <<
            new Interval(40, 50), // <<
            new Interval(60, 70), // <<
            new Interval(65, 70), // <<
            new Interval(71, 72), // i=7
        ]);
    }

    /**
     * @dataProvider providerTestInterSelectsYields
     */
    public function testInterSelectsYields($a): void
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
        $len = 0;
        foreach ($a->yieldInterSelect(15, 70) as $i) {
            $index = array_search($i, $b);
            $this->assertTrue(!in_array($index, [0, 7]));
            $len++;
        }

        $this->assertEquals($len, 6);
    }

    public function randomBuildTree(array $intervals)
    {
        $stress = getenv('KOKI_TEST_RANDOMIZE');
        $stress = $stress ? $stress : 20;

        // basic:
        foreach (range(0, $stress) as $seed) {
            shuffle($intervals);
            yield new Tree($intervals);
        }
        // add
        foreach (range(0, $stress) as $seed) {
            shuffle($intervals);
            yield $this->added($intervals);
        }
        // basic + add
        foreach (range(0, $stress) as $seed) {
            $i = $intervals;
            shuffle($i);
            $c = count($i);
            $ii = array_splice($i, 0, rand(0, $c));
            $t = new Tree($ii);
            $this->add($t, $i);
            yield $t;
        }
        // basic + delete + add
    }

    public function providerTestInterSelects3()
    {
        return $this->makeTreeProvider([
            new Interval(9, 11),
            new Interval(5, 15), // <<
            new Interval(10, 20), // <<
            new Interval(30, 40), // <<
            new Interval(40, 50), // <<
            new Interval(60, 70), // <<
            new Interval(65, 70), // <<
            new Interval(71, 72),
        ]);
    }

    /**
     * @dataProvider providerTestInterSelects3
     */
    public function testInterSelects3(Tree $a): void
    {
        $c = $a->interSelect(15, 70);
        $this->assertEquals('5-15;10-20;30-40;40-50;60-70;65-70;', $this->sig($c));
    }

    public function providerTestInterSelects2()
    {
        return $this->makeTreeProvider([
            new Interval(1, 10),
            new Interval(5, 15), // <<
            new Interval(10, 20), // <<
            new Interval(30, 40), // <<
            new Interval(40, 50), // <<
            new Interval(60, 70), // <<
            new Interval(65, 70), // <<
        ]);
    }

    /**
     * @dataProvider providerTestInterSelects2
     */
    public function testInterSelects2(Tree $a): void
    {
        $c = $a->interSelect(15, 70);
        $this->assertEquals('5-15;10-20;30-40;40-50;60-70;65-70;', $this->sig($c));
    }

    public function added(array $i)
    {
        $t = new Tree();
        foreach ($i as $e) {
            $t->add($e);
        }

        return $t;
    }

    public function providerTestInterSelects()
    {
        return $this->makeTreeProvider([
            new Interval(1, 10),
            new Interval(5, 15),
            new Interval(10, 20),
            new Interval(30, 40), // <<
            new Interval(40, 50), // <<
            new Interval(60, 70), // <<
            new Interval(65, 71),
            // new Interval(65, 70), // todo
        ]);
    }

    /**
     * @dataProvider providerTestInterSelects
     */
    public function testInterSelects1(Tree $a): void
    {
        $c = $a->interSelect(29, 62);
        $this->assertEquals('30-40;40-50;60-70;', $this->sig($c));
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

    public function providerTestSelectsAll()
    {
        return $this->makeTreeProvider([
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
    }

    /**
     * @dataProvider providerTestSelectsAll
     */
    public function testSelectsAll(Tree $a, int $count = 10): void
    {
        $this->assertEquals(count($a->all()), $count);
    }

    public function testFlowsEmpty(): void
    {
        $a = new Tree([]);
        foreach ($a->yieldSelect(1, 2) as $i) {
            $this->assertEmpty(1);
        }

        $this->assertEmpty($a->select(2, 3));
    }

    public function providerTestPerformantGenerator()
    {
        return $this->makeTreeProvider([
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
    }

    /**
     * @dataProvider providerTestPerformantGenerator
     */
    public function testPerformantGenerator(Tree $a): void
    {
        foreach ($a->yieldSelect(2, 4) as $i) {
            $this->assertEquals($i->getStart(), 3);
            $this->assertEquals($i->getEnd(), 3);
        }
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

    public function providerTestSelectsDuplicates()
    {
        return $this->makeTreeProvider([
            new Interval(3, 5),
            new Interval(1, 1),
            new Interval(4, 6),
            new Interval(3, 5),
        ]);
    }

    /**
     * @dataProvider providerTestSelectsDuplicates
     */
    public function testSelectsDuplicates(Tree $a): void
    {
        $b = $a->select(2, 6);
        $this->assertEquals(2, count($b));
    }

    public function providerTestSelectsAllRange()
    {
        return $this->makeTreeProvider([
            new Interval(4, 6),
            new Interval(1, 1),
            new Interval(1, 1),
            new Interval(3, 5),
            new Interval(3, 5),
        ]);
    }

    /**
     * @dataProvider providerTestSelectsAllRange
     */
    public function testSelectsAllRange(Tree $a): void
    {
        $b = $a->select(0, 10);
        $this->assertEquals(5, count($b));
    }

    public function add($tree, $intervals)
    {
        foreach ($intervals as $i) {
            $tree->add($i);
        }
    }
}
