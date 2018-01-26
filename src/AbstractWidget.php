<?php

namespace Brunolobo\Widgets;
use Illuminate\Http\Request;

abstract class AbstractWidget
{
    /**
     * The number of seconds before each reload.
     * False means no reload at all.
     *
     * @var int|float|bool
     */
    public $reloadTimeout = false;

    /**
     * The number of minutes before cache expires.
     * False means no caching at all.
     *
     * @var int|float|bool
     */
    public $cacheTime = false;

    /**
     * Should widget params be encrypted before sending them to /brunolobo/load-widget?
     * Turning encryption off can help with making custom reloads from javascript, but makes widget params publicly accessible.
     *
     * @var bool
     */
    public $encryptParams = true;

    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    /**
     * Placeholder for async widget.
     * You can customize it by overwriting this method.
     *
     * @return string
     */
    public function placeholder()
    {
        $wnome = (isset($this->config['widgetname'])) ? $this->config['widgetname'] : 'Carregando' ;
        return '
            <div class="row">
                <div widget class="col-md-12">
                    <div widget-body class="panel">
                        <div widget-header class="panel-header">
                            <h3><i class="fa fa-spinner fa-pulse fa-fw"></i> <strong>$wnome</strong></h3>
                        </div>
                        <div class="panel-content p-t-0">
                            <i class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i> Carregando...
                        </div>
                    </div>
                </div>
            </div>';
    }

    /**
     * Async and reloadable widgets are wrapped in container.
     * You can customize it by overriding this method.
     *
     * @return array
     */
    public function container()
    {
        return [
            'element'       => 'div',
            'attributes'    => 'style="display:inline" class="brunolobo-widget-container"',
        ];
    }

    /**
     * Cache key that is used if caching is enabled.
     *
     * @param $params
     *
     * @return string
     */
    public function cacheKey(array $params = [])
    {
        return 'brunolobo.widgets.'.serialize($params);
    }

    /**
     * Add defaults to configuration array.
     *
     * @param array $defaults
     */
    protected function addConfigDefaults(array $defaults)
    {
        $this->config = array_merge($this->config, $defaults);
    }
}
