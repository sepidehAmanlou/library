<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    use ApiResponse;

    public function handle(Request $request, Closure $next)
    {
        $user = $request->auth_user;

        if (!$user) {
            return $this->output(401,('errors.unauthorized'));
        }

        if ($user->user_category_id !== 1) {
            return $this->output(403,('errors.Access_denied_Only_admins_can_perform_this_action'));
        }

        return $next($request);
    }
}
