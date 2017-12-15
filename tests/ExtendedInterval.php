<?php

declare(strict_types=1);

namespace Tests;

use MartanLV\Koki\Interval;

/**
 * Class Interval.
 *
 * @author yourname
 */
class ExtendedInterval extends Interval
{
    public $meta;

    /**
     * undocumented function.
     *
     * @return void
     */
    public function __construct($low, $high, $meta)
    {
        parent::__construct($low, $high);
        $this->meta = $meta;
    }
}
