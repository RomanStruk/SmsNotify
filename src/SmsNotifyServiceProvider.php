<?php
namespace RomanStruk\SmsNotify;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;

class SmsNotifyServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/smsnotify.php' => config_path('smsnotify.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/smsnotify.php', 'smsnotify'
        );

        $this->app->bind(SmsNotifyInterface::class, function () {
            return new SmsNotify(config('smsnotify'));
        });
    }
}