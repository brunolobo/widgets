<?php

namespace Brunolobo\Widgets;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'brunolobo.widget';
    }

    /**
     * Get the widget group object.
     *
     * @param $name
     *
     * @return WidgetGroup
     */
    public static function group($name)
    {
        return app('brunolobo.widget-group-collection')->group($name);
    }
}
