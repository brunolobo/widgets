<?php

namespace Brunolobo\Widgets\Test;

use Brunolobo\Widgets\Test\Support\TestApplicationWrapper;
use Brunolobo\Widgets\Test\Support\TestCase;
use Brunolobo\Widgets\WidgetGroup;
use Brunolobo\Widgets\WidgetGroupCollection;

class WidgetGroupCollectionTest extends TestCase
{
    /**
     * @var WidgetGroupCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new WidgetGroupCollection(new TestApplicationWrapper());
    }

    public function testItGrantsAccessToWidgetGroup()
    {
        $groupObject = $this->collection->group('sidebar');

        $expectedObject = new WidgetGroup('sidebar', new TestApplicationWrapper());

        $this->assertEquals($expectedObject, $groupObject);
    }
}
