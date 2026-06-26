<?php

namespace Sentix\MediaManager\Middleware;

use Closure;

class CheckMediaPermissions
{
    public function handle($request, Closure $next, $permission = null)
    {
        if (! config('media.permission')) {
            return $next($request);
        }

        $permissionConfig = $this->permission($permission);
        if (! auth()->user()?->can($permissionConfig)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }

    protected function permission($key, $default = null)
    {
        $configKey = config("media.permissions.$key", $default);

        return $configKey;
    }
}
