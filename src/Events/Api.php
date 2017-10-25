<?php

namespace InnStudio\Prober\Events;

class Api
{
    private static $filters     = array();
    private static $actions     = array();
    private static $PRIORITY_ID = 'priority';
    private static $CALLBACK_ID = 'callback';

    public static function on($name, $callback, $priority = 10)
    {
        if ( ! isset(self::$actions[$name])) {
            self::$actions[$name] = array();
        }

        self::$actions[$name][] = array(
            self::$PRIORITY_ID => $priority,
            self::$CALLBACK_ID => $callback,
        );
    }

    public static function emit()
    {
        $args = \func_get_args();
        $name = $args[0];
        unset($args[0]);

        $actions = isset(self::$actions[$name]) ? self::$actions[$name] : false;

        if ( ! $actions) {
            return;
        }

        $sortArr = array();

        foreach ($actions as $k => $action) {
            $sortArr[$k] = $action[self::$PRIORITY_ID];
        }

        \array_multisort($sortArr, $actions);

        foreach ($actions as $action) {
            \call_user_func_array($action[self::$CALLBACK_ID], $args);
        }
    }

    public static function patch($name, $callback, $priority = 10)
    {
        if ( ! isset(self::$filters[$name])) {
            self::$filters[$name] = array();
        }

        self::$filters[$name][] = array(
            self::$PRIORITY_ID => $priority,
            self::$CALLBACK_ID => $callback,
        );
    }

    public static function apply()
    {
        $args = \func_get_args();

        $name   = $args[0];
        $return = $args[1];

        unset($args[0],$args[1]);

        $filters = isset(self::$filters[$name]) ? self::$filters[$name] : false;

        if ( ! $filters) {
            return $return;
        }

        $sortArr = array();

        foreach ($filters as $k => $filter) {
            $sortArr[$k] = $filter[self::$PRIORITY_ID];
        }

        \array_multisort($sortArr, $filters);

        foreach ($filters as $filter) {
            $return = \call_user_func_array($filter[self::$CALLBACK_ID], array($return, $args));
        }

        return $return;
    }
}
