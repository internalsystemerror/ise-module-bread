<?php

namespace IseBread\Entity;

use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class DatePeriod
{

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var DateTime
     */
    protected $start;

    /**
     * @ORM\Column(type="dateinterval", nullable=false)
     * @var DateInterval
     */
    protected $interval;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var DateTime
     */
    protected $end;

    /**
     * Get period start date
     *
     * @return DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set period start date
     *
     * @param DateTime $start
     * @return DatePeriod
     */
    public function setStart(DateTime $start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * Get period interval
     *
     * @return DateInterval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Set period interval
     *
     * @param DateInterval $interval
     * @return DatePeriod
     */
    public function setInterval(DateInterval $interval)
    {
        $this->interval = $interval;
        return $this;
    }

    /**
     * Get period end date
     *
     * @return DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set period end date
     *
     * @param DateTime $end
     * @return DatePeriod
     */
    public function setEnd(DateTime $end)
    {
        $this->end = $end;
        return $this;
    }
}
