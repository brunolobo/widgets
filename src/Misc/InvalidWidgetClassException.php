<?php

namespace Brunolobo\Widgets\Misc;

use Exception;

class InvalidWidgetClassException extends Exception
{
    /**
     * Exception message.
     *
     * @var string
     */
    protected $message = 'Widget class deve extender Brunolobo\Widgets\AbstractWidget class';
}
