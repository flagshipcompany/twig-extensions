<?php

use Symfony\Component\Translation\TranslatorInterface;

class TimeAgoFilter extends Twig_Extension
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
            new \Twig_SimpleFilter('timeago', array($this, 'timeAgo')),
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

            $transString = 'diff.'.($isFuture ? 'in.' : 'ago.').$val;
            $numberOfUnits = floor($time / $unit);

            $output = $this->translator->transChoice($transString, $numberOfUnits, ['%count%' => $numberOfUnits]);

            return $output;
        }
    }
}
