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
