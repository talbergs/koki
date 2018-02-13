<?php

declare(strict_types=1);

namespace MartanLV\Koki;

/**
 * Augmented tree
 * Class StaticTree.
 *
 * @author yourname
 */
class Tree extends Node
{
    public $root;

    /**
     * undocumented function.
     *
     * @param $intervals array of Interval
     *
     * @return void
     */
    public function __construct(array $intervals = [], bool $preSorted = false)
    {
        !$preSorted && usort($intervals, function (IntervalInterface $i0, IntervalInterface $i) {
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
            return;
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

        $node = new Node(
            $interval,
            $this->toTree($l_intervals),
            $this->toTree($r_intervals),
            $max
        );

        $node->root = &$this;

        return $node;
    }

    /**
     * returns intervals that exclusively fall within range given
     * to retrieve eather bound inclusevely, just modify parameters.
     *
     * @return void
     */
    public function select(int $low, int $high): array
    {
        return $this->root ? $this->root->select($low, $high) : [];
    }

    public function selectNode(int $low, int $high): array
    {
        return $this->root ? $this->root->selectNode($low, $high) : [];
    }

    /**
     * rebalance messed up tree overall.
     */
    public function balance()
    {
        // todo
    }

    /**
     * understandable var dump.
     */
    public function __debugInfo()
    {
        $arr = $this->all();

        usort($arr, function (IntervalInterface $i0, IntervalInterface $i) {
            return $i0->getStart() <=> $i->getStart();
        });

        return $arr;
    }

    public function yieldInterSelect(int $low, int $high)
    {
        return $this->root ? $this->root->yieldInterSelect($low, $high) : [];
    }

    public function interSelectNode(int $low, int $high): array
    {
        return $this->root ? $this->root->interSelectNode($low, $high) : [];
    }

    public function interSelect(int $low, int $high): array
    {
        return $this->root ? $this->root->interSelect($low, $high) : [];
    }

    public function yieldSelect(int $low, int $high)
    {
        return $this->root ? $this->root->yieldSelect($low, $high) : [];
    }

    /**
     * @return void
     */
    public function all(): array
    {
        return $this->root ? $this->root->all() : [];
    }

    /**
     * undocumented function.
     *
     * @return void
     */
    public function yieldAll()
    {
        return $this->root ? $this->root->yieldAll() : [];
    }

    public function removeRegion(int $low, int $high)
    {
        if (!$this->root) {
            return [];
        }
        foreach ($this->interSelectNode($low, $high) as $node) {
            $this->remove($node);
        }
    }

    public function remove(IntervalInterface $i)
    {
        if (!$i->left && !$i->right) {
            if ($i->root) {
                $i->root = null;
            }

            return;
        }

        if ($i->left && $i->right) {
            // todo
            return;
        }
        if ($i->left) {
            $i->interval = &$i->left->interval;
            $i->left = null;
        }

        if ($i->right) {
        }

        if ($this->max > $i->getEnd()) {
            if ($this->left) {
                $this->left->add($i);
            } else {
                $this->left = new Node($i);
            }
        } else {
            $this->max = $i->getEnd();
            if ($this->right) {
                $this->right->add($i);
            } else {
                $this->right = new Node($i);
            }
        }

        return;
        if (!$this->root) {
            return [];
        }
        $this->root->remove($i);
    }

    public function add(IntervalInterface $i)
    {
        if (!$this->root) {
            $this->root = new Node($i);

            return;
        }
        $this->root->add($i);
    }
}
