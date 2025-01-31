<?php

namespace IsmayilDev\EpochSoftDelete\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use IsmayilDev\EpochSoftDelete\EpochCast;
use IsmayilDev\EpochSoftDelete\EpochSoftDeletes;
use PHPUnit\Framework\TestCase;

class DatabaseEpochSoftDeletingTest extends TestCase
{
    public function test_deleted_at_is_added_to_casts_as_default_type()
    {
        $model = new EpochSoftDeletingModel;

        $this->assertArrayHasKey('deleted_at', $model->getCasts());
        $this->assertSame(EpochCast::class, $model->getCasts()['deleted_at']);
    }

    public function test_deleted_at_is_cast_to_carbon_instance()
    {
        $expected = Carbon::createFromFormat('Y-m-d H:i:s', '2018-12-29 13:59:39');
        $model = new EpochSoftDeletingModel(['deleted_at' => $expected->timestamp]);

        $this->assertInstanceOf(Carbon::class, $model->deleted_at);
        $this->assertTrue($expected->eq($model->deleted_at));
    }

    public function test_existing_cast_overrides_added_date_cast()
    {
        $model = new class(['deleted_at' => Carbon::now()->timestamp]) extends EpochSoftDeletingModel
        {
            protected $casts = ['deleted_at' => 'bool'];
        };

        $this->assertTrue($model->deleted_at);
    }

    public function test_existing_mutator_overrides_added_date_cast()
    {
        $model = new class(['deleted_at' => Carbon::now()->timestamp]) extends EpochSoftDeletingModel
        {
            protected function getDeletedAtAttribute()
            {
                return 'expected';
            }
        };

        $this->assertSame('expected', $model->deleted_at);
    }

    public function test_casting_to_string_overrides_automatic_date_casting_to_retain_previous_behaviour()
    {
        $model = new class(['deleted_at' => Carbon::now()->timestamp]) extends EpochSoftDeletingModel
        {
            protected $casts = ['deleted_at' => 'string'];
        };

        $this->assertSame((string) Carbon::now()->timestamp, $model->deleted_at);
    }
}

class EpochSoftDeletingModel extends Model
{
    use EpochSoftDeletes;

    protected $guarded = [];

    protected $dateFormat = 'Y-m-d H:i:s';
}
