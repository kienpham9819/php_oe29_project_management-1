<?php

namespace App\Http\Middleware;

use Closure;
use App;
use View;
use File;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lang = session()->get('lang', config('app.locale'));
        App::setLocale($lang);

        $langPath = resource_path('lang/' . App::getLocale());
        View::share('translation', collect(File::allFiles($langPath))
            ->flatMap(function ($file) {
                return [
                    ($translation = $file->getBasename('.php')) => trans($translation),
                ];
            })->toJson());

        return $next($request);
    }
}
