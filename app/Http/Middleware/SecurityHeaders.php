<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SecurityHeaders {

    public function handle($request, Closure $next) {
        $response = $next($request);

        $this->setHSTSHeader($response);
        $this->setCSPHeader($response);
        $this->setXFrameOptionsHeader($response);
        $this->setXContentTypeOptionsHeader($response);
        $this->setReferrerPolicyHeader($response);

        return $response;
    }

    protected function setHSTSHeader($response) {
        if (App::environment() !== 'production') return;

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    protected function setCSPHeader($response) {
        $response->headers->set($this->getCSPHeaderName(), join('; ', [
            $this->getCSPDefaultSrc(),
            $this->getCSPScriptSrc(),
            $this->getCSPStyleSrc(),
            $this->getCSPImgSrc(),
            $this->getCSPReportUri(),
        ]));
    }

    protected function setXFrameOptionsHeader($response) {
        $response->headers->set('X-Frame-Options', 'DENY');
    }

    protected function setXContentTypeOptionsHeader($response) {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
    }

    protected function setReferrerPolicyHeader($response) {
        $response->headers->set('Referrer-Policy', 'same-origin');
    }

    protected function getCSPHeaderName() {
        // Outside of production, only report violations, but don't block them
        if (App::environment() !== 'production') return 'Content-Security-Policy-Report-Only';

        return 'Content-Security-Policy';
    }

    protected function getCSPDefaultSrc() {
        return "default-src 'self'";
    }

    protected function getCSPScriptSrc() {
        // Outside of production, allow inline scripts (Vue dev tools)
        if (App::environment() !== 'production') return "script-src 'self' 'unsafe-inline' 'unsafe-eval'";

        return "script-src 'self' 'unsafe-eval'";
    }

    protected function getCSPStyleSrc() {
        // Outside of production, allow inline styles
        if (App::environment() !== 'production') return "style-src 'self' 'unsafe-inline'";

        return "style-src 'self'";
    }

    protected function getCSPImgSrc() {
        return "img-src 'self' data:";
    }

    protected function getCSPReportUri() {
        $reportUri = env('SENTRY_CSP_REPORT_URI', false);
        if ($reportUri) return "report-uri $reportUri&sentry_environment=" . App::environment();
        return '';
    }

}
