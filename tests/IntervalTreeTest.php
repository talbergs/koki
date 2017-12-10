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

    public function testFlowsEmpty(): void
    {
        $a = new Tree([]);
        foreach($a->yieldSelect(1, 2) as $i) {
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
        foreach($a->yieldSelect(2, 4) as $i) {
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

    public function testSelectsAll(): void
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
