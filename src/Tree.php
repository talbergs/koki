<?php

declare(strict_types=1);

namespace MartanLV\Koki;

/**
 * Augmented tree
 * Class StaticTree
 * @author yourname
 */
class Tree extends Node
{
    /**
     * undocumented function
     *
     * @param $intervals array of Interval
     * @return void
     */
    public function __construct(array $intervals, bool $preSorted = false)
    {
        !$preSorted && usort($intervals, function(IntervalInterface $i0, IntervalInterface $i) {
            return $i0->getStart() <=> $i->getStart();
        });
        $this->root = $this->toTree($intervals);
    }

    /**
     * An augmented tree can be built from a simple ordered tree,
     * for example a binary search tree or self-balancing binary search tree,
     * ordered by the 'low' values of the intervals.
     */
    public function toTree(array $intervals)
    {
        $max = 0;
        $len = count($intervals);
        $mid = (int) floor($len / 2);

        if (!$len) {
            return null;
        }

        array_walk($intervals, function (IntervalInterface $el) use (&$max) {
            if ($max < $el->getEnd()) {
                $max = $el->getEnd();
            }
        });
        $l_intervals = array_splice($intervals, 0, $mid);
        $r_intervals = array_splice($intervals, -$mid);
        $interval = $intervals ? reset($intervals) : array_pop($l_intervals);
        $interval = $interval ?? array_pop($r_intervals);

        return new Node(
            $interval,
            $this->toTree($l_intervals),
            $this->toTree($r_intervals),
            $max
        );
    }

    /**
     * returns intervals that exclusively fall within range given
     * to retrieve eather bound inclusevely, just modify parameters
     *
     * @return void
     */
    public function select(int $low, int $high): array
    {
        return $this->root ? $this->root->select($low, $high) : [];
    }

    public function yieldSelect(int $low, int $high)
    {
        return $this->root ? $this->root->yieldSelect($low, $high) : [];
    }
}
