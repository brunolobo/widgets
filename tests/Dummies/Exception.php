<?php

namespace Brunolobo\Widgets\Test\Dummies;

use Brunolobo\Widgets\AbstractWidget;

class Exception extends AbstractWidget
{
    public function run()
    {
        return 'Exception widget was executed instead of predefined php class';
    }
}
