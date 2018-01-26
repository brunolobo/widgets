<?php

namespace Brunolobo\Widgets\Test\Dummies;

use Brunolobo\Widgets\AbstractWidget;

class TestWidgetWithParamsInRun extends AbstractWidget
{
    public function run($flag)
    {
        return 'TestWidgetWithParamsInRun was executed with $flag = '.$flag;
    }

    public function placeholder()
    {
        return 'Placeholder here!';
    }
}
