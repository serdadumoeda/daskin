<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('isRouteActive')) {
    /**
     * Memeriksa apakah nama rute saat ini dimulai dengan awalan tertentu.
     * Ini berguna untuk menandai menu dan submenu sebagai 'aktif'.
     *
     * @param string $routeNamePrefix Awalan nama rute (misal: 'users', 'dashboard').
     * @return bool
     */
    function isRouteActive(string $routeNamePrefix): bool
    {
        return str_starts_with(Route::currentRouteName(), $routeNamePrefix);
    }
}