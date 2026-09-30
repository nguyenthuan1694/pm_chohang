<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
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
        Paginator::useBootstrapFive();

        Event::listen(Login::class, function (Login $event) {
            if ($event->user instanceof User) {
                ActivityLog::createLog(
                    description: "Đăng nhập hệ thống thành công",
                    module: 'auth',
                    action: 'login',
                    subject: $event->user,
                    user: $event->user,
                );
            }
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user instanceof User) {
                ActivityLog::createLog(
                    description: "Đăng xuất khỏi hệ thống",
                    module: 'auth',
                    action: 'logout',
                    subject: $event->user,
                    user: $event->user,
                );
            }
        });
    }
}
