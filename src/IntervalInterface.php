<?php

declare(strict_types=1);

namespace MartanLV\Koki;

interface IntervalInterface
{
    public function getStart(): int;

    public function getEnd(): int;
}
