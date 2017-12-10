<?php
declare(strict_types=1);

namespace Tests;

use MartanLV\Koki\IntervalInterface;

/**
 * Class Interval
 * @author yourname
 */
class ImplementedInterval implements IntervalInterface
{
    protected $low;
    protected $high;
    public $meta;

    /**
     * undocumented function
     *
     * @return void
     */
    public function __construct($low, $high, $meta)
    {
        $this->high = $high;
        $this->low = $low;
        $this->meta = $meta;
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
