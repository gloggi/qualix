<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Laracasts\Cypress\Controllers\CypressController as LaravelCypressController;

class CypressController extends LaravelCypressController {

    public function createSnapshot($name = 'cypress_savepoint') {
        Artisan::call("snapshot:create $name");
        return $name;
    }

    public function restoreSnapshot($name = 'cypress_savepoint') {
        Artisan::call("snapshot:load $name");
    }

    public function cleanupSnapshots($name = 'cypress_savepoint') {
        Artisan::call("snapshot:cleanup --keep=0");
    }

    public function generate(Request $request)
    {
        return factory($request->input('model'))
            ->times($request->input('times'))
            ->make($request->input('attributes'));
    }

}
