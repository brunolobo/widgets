<?php

namespace Brunolobo\Widgets\Test\Support;

use Brunolobo\Widgets\WidgetId;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        WidgetId::reset();
    }

    public function ajaxUrl($widgetName, $widgetParams = [], $id = 1)
    {
        return '/brunolobo/load-widget?'.http_build_query([
            'id'     => $id,
            'name'   => $widgetName,
            'params' => json_encode($widgetParams),
        ]);
    }
}
