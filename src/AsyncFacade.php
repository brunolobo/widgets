<?php

namespace Brunolobo\Widgets;

class AsyncFacade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'brunolobo.async-widget';
    }
}
