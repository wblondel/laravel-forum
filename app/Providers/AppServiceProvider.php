<?php

namespace App\Providers;

use App\Notifications\DatabaseApproachingMaxConnections;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\DatabaseBusy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict();

        DB::prohibitDestructiveCommands(! $this->app->isLocal() && ! $this->app->runningUnitTests());

        Event::listen(function (DatabaseBusy $event) {
            Notification::route('mail', 'dev@example.com')
                ->notify(new DatabaseApproachingMaxConnections(
                    $event->connectionName,
                    $event->connections
                ));
        });
    }
}
