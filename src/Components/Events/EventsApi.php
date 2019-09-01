<?php

namespace InnStudio\Prober\Components\Events;

class EventsApi
{
    private static $events      = array();
    private static $PRIORITY_ID = 'priority';
    private static $CALLBACK_ID = 'callback';

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

    public static function emit()
    {
        $args = \func_get_args();

        $name   = $args[0];
        $return = isset($args[1]) ? $args[1] : null;

        unset($args[0], $args[1]);

        $events = isset(self::$events[$name]) ? self::$events[$name] : false;

        if ( ! $events) {
            return $return;
        }

        $sortArr = array();

        foreach ($events as $k => $filter) {
            $sortArr[$k] = $filter[self::$PRIORITY_ID];
        }

        \array_multisort($sortArr, $events);

        foreach ($events as $filter) {
            $return = \call_user_func_array($filter[self::$CALLBACK_ID], array($return, $args));
        }

        return $return;
    }
}
