<?php

namespace IsmayilDev\EpochSoftDelete;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class EpochSoftDeleteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blueprint::macro('epochSoftDeletes', function () {
            $this->integer('deleted_at')->default(0);
        });
    }
}
