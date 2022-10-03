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
            $this->getCSPConnectSrc(),
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
        return "script-src 'self' 'unsafe-eval'";
    }

    protected function getCSPStyleSrc() {
        $nonce = app('csp-nonce');
        return "style-src 'self' 'nonce-${nonce}'";
    }

    protected function getCSPImgSrc() {
        return "img-src 'self' data:";
    }

    protected function getCSPConnectSrc() {
        if (!config('app.collaboration.enabled')) return '';

        $sentryUrl = '';
        if (config('app.sentry.mix.vue_dsn')) {
            $parsed = parse_url(config('app.sentry.mix.vue_dsn'));
            $sentryUrl = $parsed['scheme'] . '://' . $parsed['host'] . (isset($parsed['port']) ? ':' . $parsed['port'] : '');
        }
        return "connect-src 'self' $sentryUrl " . config('app.collaboration.signaling_servers');
    }

    protected function getCSPReportUri() {
        $reportUri = config('app.sentry.csp_report_uri', false);
        if ($reportUri) return "report-uri $reportUri&sentry_environment=" . App::environment();
        return '';
    }
}
