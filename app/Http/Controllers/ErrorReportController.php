<?php

namespace App\Http\Controllers;

use App\Http\Requests\ErrorReportRequest;
use GuzzleHttp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ErrorReportController extends Controller {

    /**
     * Send an error report to Sentry.
     *
     * @param ErrorReportRequest $request
     * @return RedirectResponse
     */
    public function submit(ErrorReportRequest $request) {
        $params = $request->validated();
        $request->session()->flash('previousUrl', $params['previousUrl']);
        $client = app(GuzzleHttp\Client::class);
        $url = config('app.sentry.user_feedback_url');
        $dsn = config('sentry.dsn');
        $client->post($url, [
            'headers' => ['Authorization' => 'DSN ' . $dsn],
            'form_params' => [
                'event_id' => $params['eventId'],
                'name' => $params['name'],
                'email' => $params['email'],
                'comments' => $params['description'],
            ],
        ]);
        return Redirect::route('errorReport.after');
    }

    /**
     * Display a message to the user to thank him for submitting a report.
     *
     * @return View
     */
    public function after(Request $request) {
        return view('pages.after-error-submit', ['previousUrl' => $request->session()->get('previousUrl', '/')]);
    }
}
