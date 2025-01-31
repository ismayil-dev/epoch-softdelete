<?php

namespace IsmayilDev\EpochSoftDelete\Tests\Unit;

use Illuminate\Support\Carbon;
use IsmayilDev\EpochSoftDelete\EpochSoftDeletes;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseEpochSoftDeletingTraitTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function test_delete_sets_epoch_deleted_at_column()
    {
        $model = m::mock(DatabaseEpochSoftDeletingTraitStub::class);
        $model->makePartial();
        $model->shouldReceive('newModelQuery')->andReturn($query = m::mock(\stdClass::class));
        $query->shouldReceive('where')->once()->with('id', '=', 1)->andReturn($query);
        $query->shouldReceive('update')->once()->with([
            'deleted_at' => Carbon::now()->timestamp,
            'updated_at' => 'date-time',
        ]);
        $model->shouldReceive('syncOriginalAttributes')->once()->with([
            'deleted_at',
            'updated_at',
        ]);
        $model->shouldReceive('usesTimestamps')->once()->andReturn(true);
        $model->delete();

        $this->assertIsInt($model->deleted_at);
    }

    public function test_restore()
    {
        $model = m::mock(DatabaseEpochSoftDeletingTraitStub::class);
        $model->makePartial();
        $model->shouldReceive('fireModelEvent')->with('restoring')->andReturn(true);
        $model->shouldReceive('save')->once();
        $model->shouldReceive('fireModelEvent')->with('restored', false)->andReturn(true);

        $model->restore();

        $this->assertEquals(0, $model->deleted_at);
    }

    public function test_restore_cancel()
    {
        $model = m::mock(DatabaseEpochSoftDeletingTraitStub::class);
        $model->makePartial();
        $model->shouldReceive('fireModelEvent')->with('restoring')->andReturn(false);
        $model->shouldReceive('save')->never();

        $this->assertFalse($model->restore());
    }
}

class DatabaseEpochSoftDeletingTraitStub
{
    use EpochSoftDeletes;

    public $deleted_at;

    public bool $timestamps = true;

    public $exists = false;

    public function newQuery()
    {
        //
    }

    public function getKey()
    {
        return 1;
    }

    public function getKeyName()
    {
        return 'id';
    }

    public function save()
    {
        //
    }

    public function delete()
    {
        return $this->performDeleteOnModel();
    }

    public function fireModelEvent()
    {
        //
    }

    public function freshTimestamp()
    {
        return Carbon::now();
    }

    public function fromDateTime()
    {
        return 'date-time';
    }

    public function getUpdatedAtColumn()
    {
        return 'updated_at';
    }

    public function setKeysForSaveQuery($query)
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery());

        return $query;
    }

    protected function getKeyForSaveQuery()
    {
        return 1;
    }
}
