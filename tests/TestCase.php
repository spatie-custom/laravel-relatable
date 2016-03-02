<?php

namespace Spatie\Relatable\Test;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\Relatable\Relatable;
use Spatie\Relatable\Test\TestModels\{ HasFruitAsRelatedContent, Lime };
use Spatie\Relatable\Test\TestModels\Strawberry;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase()
    {
        file_put_contents(__DIR__.'/temp/database.sqlite', null);

        $this->app['db']->connection()->getSchemaBuilder()->create('has_fruit_as_related_contents', function (Blueprint $table) {
            $table->increments('id');
        });

        $this->app['db']->connection()->getSchemaBuilder()->create('limes', function (Blueprint $table) {
            $table->increments('id');
        });

        $this->app['db']->connection()->getSchemaBuilder()->create('strawberries', function (Blueprint $table) {
            $table->increments('id');
        });

        HasFruitAsRelatedContent::create(['id' => 1]);
        Lime::create(['id' => 1]);
        Strawberry::create(['id' => 1]);

        include_once '__DIR__'.'/../database/migrations/create_relatables_table.php.stub';

        (new \CreateRelatablesTable())->up();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => __DIR__.'/temp/database.sqlite',
            'prefix' => '',
        ]);
    }

    protected function modelIsRelatedToSource(Model $related, Model $source) : bool
    {
        return (bool) Relatable::where([
            'source_id' => $source->getKey(),
            'source_type' => $source->getMorphClass(),
            'related_id' => $related->getKey(),
            'related_type' => $related->getMorphClass(),
        ])->first();
    }

    protected function assertModelIsRelatedToSource(Model $related, Model $source)
    {
        $this->assertTrue($this->modelIsRelatedToSource($related, $source));
    }

    protected function assertModelIsntRelatedToSource(Model $related, Model $source)
    {
        $this->assertFalse($this->modelIsRelatedToSource($related, $source));
    }

    protected function assertRelatedCollectionContains(Collection $collection, Model $related)
    {
        $this->assertTrue($collection->contains(function ($key, Model $item) use ($related) {
            return $item->id === $related->id && get_class($item) === $related->getMorphClass();
        }));
    }
}
