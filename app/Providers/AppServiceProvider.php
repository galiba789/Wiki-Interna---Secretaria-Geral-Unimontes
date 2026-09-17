<?php

namespace App\Providers;

use App\Models\SuggestionNotification;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view): void {
            $unreadSuggestionCount = 0;

            if (auth()->check()) {
                $query = SuggestionNotification::whereNull('read_at');

                if (! auth()->user()->is_admin) {
                    $query->where('user_id', auth()->id());
                }

                $unreadSuggestionCount = $query->count();
            }

            $view->with('unreadSuggestionCount', $unreadSuggestionCount);
        });
    }
}
