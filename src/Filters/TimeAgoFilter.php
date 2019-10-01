<?php

namespace Flagship\Components\Twig\Extensions\Filters;

use Symfony\Component\Translation\TranslatorInterface;

class TimeAgoFilter extends \Twig_Extension
{
    public static $units = [
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second',
    ];

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            new \Twig\TwigFilter('timeago', array($this, 'timeAgo')),
        );
    }

    public function getName()
    {
        return 'timeago';
    }

    public function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);
        $isFuture = $time < 0;
        $time = abs($time);

        foreach (self::$units as $unit => $val) {
            if ($time < $unit) {
                continue;
            }

            $dayDiff = $this->getDays($datetime);

            $transString = ($dayDiff !== false) ? $dayDiff : 'diff.'.($isFuture ? 'in.' : 'ago.').$val;

            $numberOfUnits = ceil($time / $unit);

            $output = $this->translator->transChoice($transString, $numberOfUnits, ['%count%' => $numberOfUnits]);

            return $output;
        }

        return $this->translator->transChoice('diff.ago.second', 0, ['%count%' => 0]);
    }

    protected function getDays($date)
    {
        $dateDetails = date_parse($date);
        if (false == $dateDetails['hour'] && $date == date('Y-m-d', strtotime(('-1 day')))) {
            return 'yesterday';
        }

        if (false == $dateDetails['hour'] && $date == date('Y-m-d', strtotime(('+1 day')))) {
            return 'tomorrow';
        }

        if (false == $dateDetails['hour'] && $date == date('Y-m-d')) {
            return 'today';
        }

        return false;
    }
}
