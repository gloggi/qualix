<?php
if (! function_exists('route_method')) {
    /**
     * Get the HTTP method of a named route.
     *
     * @param  array|string  $name
     * @return string
     */
    function route_method($name)
    {
        return head(app('router')->getRoutes()->getByName($name)->methods());
    }
}

if (! function_exists('csp_nonce')) {
    /**
     * Get the CSP nonce for this request, in order to confirm the validity of inline scripts.
     *
     * @return string
     */
    function csp_nonce(): string
    {
        return app('csp-nonce');
    }
}
