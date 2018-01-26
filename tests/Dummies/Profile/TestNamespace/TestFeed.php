<?php

namespace Brunolobo\Widgets\Test\Dummies\Profile\TestNamespace;

use Brunolobo\Widgets\AbstractWidget;

class TestFeed extends AbstractWidget
{
    protected $slides = 6;

    public function run()
    {
        return 'Feed was executed with $slides = '.$this->slides;
    }
}
