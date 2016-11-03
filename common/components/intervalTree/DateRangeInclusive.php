<?php

namespace common\components\intervalTree;

use DateTime;
use DateInterval;

/**
 * Date range which includes intersecting dates.
 */
class DateRangeInclusive extends DateRangeExclusive
{
    /**
     * @param \DateTime     $start
     * @param \DateTime     $end
     * @param \DateInterval $step
     */
    public function __construct(DateTime $start, DateTime $end = null, DateInterval $step = null, $id = null)
    {
        parent::__construct($start, $end, $step, $id);
        $this->end->add($this->step);
    }
}
