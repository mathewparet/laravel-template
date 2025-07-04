<?php

namespace App\Http\Middleware;

use App\Models\AgencyCredit;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'system' => [
                'flash' => Session::get('flash'),
            ],
            'modules' => $this->getUserModules(),
        ];
    }

    private function getUserModules(): array
    {
        return Cache::remember('modules.items.' . Auth::id(), now()->addMinutes(5), function() {
                return collect(config('modules.items'))
                    ->flatMap(fn (string $module) => collect($module::globalAbilities())
                                    ->filter(fn($isEnabled, $ability) => $isEnabled)
                                    ->keys()
                                    ->map(fn($ability) => $module . '::' . $ability)
                            )
                    ->values()
                    ->all();
        });
    }
}
