<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class E2ETestingController
{
    public function __construct()
    {
        abort_unless(app()->environment('testing'), 404);
    }

    public function csrfToken()
    {
        return response()->json(csrf_token());
    }

    public function login(Request $request)
    {
        $attributes = $request->input('attributes', []);

        if (empty($attributes)) {
            $user = $this->factoryBuilder(
                $this->userClassName(),
                $request->input('state', [])
            )->create();
        } else {
            $user = app($this->userClassName())
                ->newQuery()
                ->where($attributes)
                ->first();

            if (!$user) {
                $user = $this->factoryBuilder(
                    $this->userClassName(),
                    $request->input('state', [])
                )->create($attributes);
            }
        }

        $user->load($request->input('load', []));

        return tap($user, function ($user) {
            auth()->login($user);
            $user->setHidden([])->setVisible([]);
        });
    }

    public function logout()
    {
        auth()->logout();
    }

    public function create(Request $request)
    {
        return $this->factoryBuilder(
            $request->input('model'),
            $request->input('state', [])
        )
            ->count(intval($request->input('count', 1)))
            ->create($request->input('attributes'))
            ->each(fn($model) => $model->setHidden([])->setVisible([]))
            ->load($request->input('load', []))
            ->pipe(function ($collection) {
                return $collection->count() > 1
                    ? $collection
                    : $collection->first();
            });
    }

    public function generate(Request $request)
    {
        return $request->input('model')::factory()
            ->count($request->input('times'))
            ->make($request->input('attributes'));
    }

    public function artisan(Request $request)
    {
        Artisan::call(
            $request->input('command'),
            $request->input('parameters', [])
        );
    }

    public function runPhp(Request $request)
    {
        $code = $request->input('command');
        if ($code[-1] !== ';') {
            $code .= ';';
        }
        if (!Str::contains($code, 'return')) {
            $code = 'return ' . $code;
        }
        return response()->json(['result' => eval($code)]);
    }

    public function createSnapshot($name = 'e2e_snapshot')
    {
        Artisan::call("snapshot:create $name");
        return $name;
    }

    public function restoreSnapshot($name = 'e2e_snapshot')
    {
        Artisan::call("snapshot:load $name");
    }

    public function cleanupSnapshots()
    {
        Artisan::call('snapshot:cleanup --keep=0');
    }

    protected function userClassName()
    {
        return config('auth.providers.users.model');
    }

    protected function factoryBuilder($model, $states = [])
    {
        $factory = $model::factory();
        $states  = Arr::wrap($states);
        foreach ($states as $state => $attributes) {
            if (is_int($state)) {
                $state      = $attributes;
                $attributes = [];
            }
            $attributes = Arr::wrap($attributes);
            $factory    = $factory->{$state}(...$attributes);
        }
        return $factory;
    }
}
