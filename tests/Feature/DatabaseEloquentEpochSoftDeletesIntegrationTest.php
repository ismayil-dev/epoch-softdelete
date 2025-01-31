<?php

namespace IsmayilDev\EpochSoftDelete\Tests\Feature;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use IsmayilDev\EpochSoftDelete\EpochSoftDeletes;
use IsmayilDev\EpochSoftDelete\EpochSoftDeleteServiceProvider;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentEpochSoftDeletesIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $db = new DB;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $db->bootEloquent();
        $db->setAsGlobal();
        $app = App::getFacadeApplication();
        $serviceProvider = new EpochSoftDeleteServiceProvider($app);
        $serviceProvider->boot();

        $this->createSchema();
    }

    /**
     * Setup the database schema.
     *
     * @return void
     */
    public function createSchema()
    {
        $this->schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('email');
            $table->timestamps();
            $table->epochSoftDeletes();

            $table->unique(['email', 'deleted_at']);
        });
    }

    public function test_soft_deletes_store_epoch_timestamp()
    {
        Carbon::setTestNow($now = Carbon::now());
        $this->createUsers();

        $user = EpochSoftDeletesTestUser::find(1);
        $user->delete();

        $this->assertEquals($now->timestamp, $user->getAttributes()['deleted_at']);
        $this->assertInstanceOf(Carbon::class, $user->getOriginal('deleted_at'));
    }

    public function test_soft_deletes_are_not_retrieved()
    {
        $this->createUsers();

        $user = EpochSoftDeletesTestUser::find(1);
        $user->delete();

        $users = EpochSoftDeletesTestUser::all();
        $this->assertCount(2, $users);

        $trashedUsers = EpochSoftDeletesTestUser::withTrashed()->get();
        $this->assertCount(3, $trashedUsers);
    }

    public function test_restoring_soft_deleted_item()
    {
        $this->createUsers();

        $user = EpochSoftDeletesTestUser::find(1);
        $user->delete();
        $user->restore();

        $this->assertEquals(0, $user->getAttributes()['deleted_at']);

        $restoredUser = EpochSoftDeletesTestUser::find(1);
        $this->assertNotNull($restoredUser);
    }

    public function test_unique_constraints_after_soft_delete()
    {
        $this->createUsers();

        $user = EpochSoftDeletesTestUser::find(1);
        $user->delete();

        $duplicateUser = new EpochSoftDeletesTestUser;
        $duplicateUser->email = $user->email;

        $this->expectNotToPerformAssertions();
        $duplicateUser->save();
    }

    /**
     * Helpers...
     *
     * @return EpochSoftDeletesTestUser[]
     */
    protected function createUsers()
    {
        $taylor = EpochSoftDeletesTestUser::create(['id' => 1, 'email' => 'taylorotwell@gmail.com']);
        $abigail = EpochSoftDeletesTestUser::create(['id' => 2, 'email' => 'abigailotwell@gmail.com']);
        $ismayil = EpochSoftDeletesTestUser::create(['id' => 3, 'email' => 'me@ismayil.dev']);

        return [$taylor, $abigail, $ismayil];
    }

    /**
     * Get a schema builder instance.
     *
     * @return Builder
     */
    protected function schema()
    {
        return $this->connection()->getSchemaBuilder();
    }

    /**
     * Get a database connection instance.
     *
     * @return ConnectionInterface
     */
    protected function connection()
    {
        return Eloquent::getConnectionResolver()->connection();
    }
}

class EpochSoftDeletesTestUser extends Eloquent
{
    use EpochSoftDeletes;

    protected $table = 'users';

    protected $guarded = [];
}
