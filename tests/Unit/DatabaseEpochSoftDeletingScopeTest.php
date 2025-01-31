<?php

namespace IsmayilDev\EpochSoftDelete\Tests\Unit;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use IsmayilDev\EpochSoftDelete\EpochSoftDeletingScope;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseEpochSoftDeletingScopeTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function test_applying_scope_to_a_builder()
    {
        $scope = m::mock(EpochSoftDeletingScope::class.'[extend]');
        $builder = m::mock(EloquentBuilder::class);
        $model = m::mock(Model::class);
        $model->shouldReceive('getQualifiedDeletedAtColumn')->once()->andReturn('table.deleted_at');
        $builder->shouldReceive('where')->once()->with('table.deleted_at', 0);

        $scope->apply($builder, $model);
    }

    public function test_restore_extension()
    {
        $builder = new EloquentBuilder(new BaseBuilder(
            m::mock(ConnectionInterface::class),
            m::mock(Grammar::class),
            m::mock(Processor::class)
        ));
        $scope = new EpochSoftDeletingScope;
        $scope->extend($builder);
        $callback = $builder->getMacro('restore');
        $givenBuilder = m::mock(EloquentBuilder::class);
        $givenBuilder->shouldReceive('withTrashed')->once();
        $givenBuilder->shouldReceive('getModel')->once()->andReturn($model = m::mock(stdClass::class));
        $model->shouldReceive('getDeletedAtColumn')->once()->andReturn('deleted_at');
        $givenBuilder->shouldReceive('update')->once()->with(['deleted_at' => 0]);

        $callback($givenBuilder);
    }

    public function test_only_trashed_extension()
    {
        $mockedBaseBuilder = new BaseBuilder(
            m::mock(ConnectionInterface::class),
            m::mock(Grammar::class),
            m::mock(Processor::class),
        );
        $builder = new EloquentBuilder($mockedBaseBuilder);
        $model = m::mock(Model::class);
        $model->makePartial();
        $scope = m::mock(EpochSoftDeletingScope::class.'[remove]');
        $scope->extend($builder);
        $callback = $builder->getMacro('onlyTrashed');
        $givenBuilder = m::mock(EloquentBuilder::class);
        $givenBuilder->shouldReceive('getQuery')->andReturn($query = m::mock(stdClass::class));
        $givenBuilder->shouldReceive('getModel')->andReturn($model);
        $givenBuilder->shouldReceive('withoutGlobalScope')->with($scope)->andReturn($givenBuilder);
        $model->shouldReceive('getQualifiedDeletedAtColumn')->andReturn('table.deleted_at');
        $givenBuilder->shouldReceive('where')->once()->with('table.deleted_at', '!=', 0);
        $result = $callback($givenBuilder);

        $this->assertEquals($givenBuilder, $result);
    }

    public function test_without_trashed_extension()
    {
        $mockedBaseBuilder = new BaseBuilder(
            m::mock(ConnectionInterface::class),
            m::mock(Grammar::class),
            m::mock(Processor::class),
        );
        $builder = new EloquentBuilder($mockedBaseBuilder);
        $model = m::mock(Model::class);
        $model->makePartial();
        $scope = m::mock(EpochSoftDeletingScope::class.'[remove]');
        $scope->extend($builder);
        $callback = $builder->getMacro('withoutTrashed');
        $givenBuilder = m::mock(EloquentBuilder::class);
        $givenBuilder->shouldReceive('getQuery')->andReturn($query = m::mock(stdClass::class));
        $givenBuilder->shouldReceive('getModel')->andReturn($model);
        $givenBuilder->shouldReceive('withoutGlobalScope')->with($scope)->andReturn($givenBuilder);
        $model->shouldReceive('getQualifiedDeletedAtColumn')->andReturn('table.deleted_at');
        $givenBuilder->shouldReceive('where')->once()->with('table.deleted_at', 0);
        $result = $callback($givenBuilder);

        $this->assertEquals($givenBuilder, $result);
    }
}
