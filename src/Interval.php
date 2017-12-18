<?php
declare(strict_types=1);

namespace MartanLV\Koki;

/**
 * Class Interval
 * @author yourname
 */
class Interval implements IntervalInterface
{
    /**
     * The low value of an interval is used as key to maintain order in BST.
     * The insert and delete operations are same as insert and delete in self-balancing BST used.
     */
    protected $low;
    protected $high;

    /**
     * undocumented function
     *
     * @return void
     */
    public function __construct($low, $high)
    {
        $this->high = $high;
        $this->low = $low;
    }

    public function getEnd(): int
    {
        return $this->high;
    }

    public function getStart(): int
    {
        return $this->low;
    }
}
