<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string|null
     */
    protected function redirectTo($request): ?string
    {
        // Since we are not using Fortify's views, the login route has no name.
        // This means that calling route('login') would throw an exception. For this reason, we use the url() method.
        return $request->expectsJson() ? null : url('login');
    }
}
