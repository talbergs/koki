<?php

declare(strict_types=1);

namespace MartanLV\Koki;

/**
 * Class Node.
 *
 * @author yourname
 */
class Node
{
    public $root;
    /**
     * @var Interval
     */
    public $interval;
    /**
     * @var int
     */
    public $max;
    /**
     * @var Node
     */
    public $left;
    /**
     * @var Node
     */
    public $right;

    /**
     * undocumented function.
     *
     * @return void
     */
    public function __construct(IntervalInterface $interval, $left = null, $right = null, $max = 0)
    {
        $this->interval = $interval;
        $this->left = $left;
        $this->right = $right;
        $this->max = $max ? $max : $interval->getEnd();
    }

    /**
     * returns intervals that fall within interval range.
     *
     * @return void
     */
    public function all(): array
    {
        return iterator_to_array($this->yieldAll(), false);
    }

    /**
     * undocumented function.
     *
     * @return void
     */
    public function yieldAll()
    {
        return $this->yieldSelect(-1, $this->max + 1);
    }

    /**
     * returns intervals that touches a given range.
     *
     * @return void
     */
    public function interSelectNode(int $low, int $high): array
    {
        return iterator_to_array($this->yieldInterSelectNode($low, $high), false);
    }

    /**
     * returns intervals that touches a given range.
     *
     * @return void
     */
    public function interSelect(int $low, int $high): array
    {
        return iterator_to_array($this->yieldInterSelect($low, $high), false);
    }

    /**
     * returns intervals that fall within interval range.
     *
     * @return void
     */
    public function select(int $low, int $high): array
    {
        return iterator_to_array($this->yieldSelect($low, $high), false);
    }

    public function selectNode(int $low, int $high): array
    {
        return iterator_to_array($this->yieldSelectNode($low, $high), false);
    }

    /**
     * returns intervals that touches a given range.
     *
     * @return generator
     */
    public function yieldInterSelectNode(int $low, int $high)
    {
        $edgeR = $high >= $this->interval->getStart() && $high <= $this->interval->getEnd();
        $edgeL = $low >= $this->interval->getStart() && $low <= $this->interval->getEnd();
        $part = $this->interval->getStart() >= $low && $this->interval->getEnd() <= $high;
        $whole = $this->interval->getStart() <= $low && $this->interval->getEnd() >= $high;

        $currentNodeMatches = $edgeR || $edgeL || $part || $whole;
        if ($currentNodeMatches) {
            yield $this;
        }

        if ($this->right && $this->interval->getStart() <= $high) {
            yield from $this->right->yieldInterSelectNode($low, $high);
        }

        if ($this->left && $this->left->max >= $low) {
            yield from $this->left->yieldInterSelectNode($low, $high);
        }
    }

    /**
     * returns intervals that touches a given range.
     *
     * @return generator
     */
    public function yieldInterSelect(int $low, int $high)
    {
        $edgeR = $high >= $this->interval->getStart() && $high <= $this->interval->getEnd();
        $edgeL = $low >= $this->interval->getStart() && $low <= $this->interval->getEnd();
        $part = $this->interval->getStart() >= $low && $this->interval->getEnd() <= $high;
        $whole = $this->interval->getStart() <= $low && $this->interval->getEnd() >= $high;

        $currentNodeMatches = $edgeR || $edgeL || $part || $whole;
        if ($currentNodeMatches) {
            yield $this->interval;
        }

        if ($this->right && $this->interval->getStart() <= $high) {
            yield from $this->right->yieldInterSelect($low, $high);
        }

        if ($this->left && $this->left->max >= $low) {
            yield from $this->left->yieldInterSelect($low, $high);
        }
    }

    /**
     * returns intervals that fall within interval range.
     *
     * @return generator
     */
    public function yieldSelectNode(int $low, int $high)
    {
        /*
         * does current node matches?
         */
        if ($this->interval->getEnd() < $high && $this->interval->getStart() > $low) {
            yield $this;
        }

        /*
         * since the node's low value is less than the "select end" value,
         * we must search in the right subtree. If it exists.
         */
        if ($this->right && $this->interval->getStart() < $high) {
            yield from $this->right->yieldSelectNode($low, $high);
        }
        /*
         * If the left subtree's max exceeds the quiery's low value,
         * so we must search the left subtree as well.
         */
        if ($this->left && $this->left->max > $low) {
            yield from $this->left->yieldSelectNode($low, $high);
        }
    }

    /**
     * returns intervals that fall within interval range.
     *
     * @return generator
     */
    public function yieldSelect(int $low, int $high)
    {
        /*
         * does current node matches?
         */
        if ($this->interval->getEnd() < $high && $this->interval->getStart() > $low) {
            yield $this->interval;
        }

        /*
         * since the node's low value is less than the "select end" value,
         * we must search in the right subtree. If it exists.
         */
        if ($this->right && $this->interval->getStart() < $high) {
            yield from $this->right->yieldSelect($low, $high);
        }
        /*
         * If the left subtree's max exceeds the quiery's low value,
         * so we must search the left subtree as well.
         */
        if ($this->left && $this->left->max > $low) {
            yield from $this->left->yieldSelect($low, $high);
        }
    }

    public function remove()
    {
        if ($this->max > $i->getEnd()) {
            if ($this->left) {
                $this->left->add($i);
            } else {
                $this->left = new self($i);
            }
        } else {
            $this->max = $i->getEnd();
            if ($this->right) {
                $this->right->add($i);
            } else {
                $this->right = new self($i);
            }
        }
    }

    public function add(IntervalInterface $i)
    {
        if ($this->max > $i->getEnd()) {
            if ($this->left) {
                $this->left->add($i);
            } else {
                $this->left = new self($i);
                $this->left->root = &$this;
            }
        } else {
            $this->max = $i->getEnd();
            if ($this->right) {
                $this->right->add($i);
            } else {
                $this->right = new self($i);
                $this->right->root = &$this;
            }
        }
    }
}
