<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use MeiliSearch\MeiliSearch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /** Fix for meilisearch indexes */
        if (class_exists(MeiliSearch::class)) {
            $client = app(\MeiliSearch\Client::class);
            $config = config('scout.meilisearch.settings');
            collect($config)
                ->each(function ($settings, $class) use ($client) {
                    $model = new $class;
                    $index = $client->index($model->searchableAs());
                    collect($settings)
                        ->each(function ($params, $method) use ($index) {
                            $index->{$method}($params);
                        });
                });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('recursive', function () {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        });
    }
}
