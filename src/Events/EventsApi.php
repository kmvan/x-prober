<?php

namespace InnStudio\Prober\Events;

class EventsApi
{
    private static $PRIORITY_ID = 'priority';
    private static $CALLBACK_ID = 'callback';
    private static $events      = array();

    public static function on($name, $callback, $priority = 10)
    {
        if ( ! isset(self::$events[$name])) {
            self::$events[$name] = array();
        }

        self::$events[$name][] = array(
            self::$PRIORITY_ID => $priority,
            self::$CALLBACK_ID => $callback,
        );
    }

    public static function emit($name, $returns = null)
    {
        $events = isset(self::$events[$name]) ? self::$events[$name] : false;

        if ( ! $events) {
            return $returns;
        }

        // sort filters by priority
        $sortArr = \array_map(function ($filter) {
            return $filter[self::$PRIORITY_ID];
        }, $events);

        \array_multisort(
            $sortArr,
            \SORT_ASC,
            \SORT_NUMERIC,
            $events
        );

        foreach ($events as $filter) {
            $args = \func_get_args();
            unset($args[0]);
            $returns = \call_user_func_array($filter[self::$CALLBACK_ID], $args);
        }

        return $returns;
    }
}
