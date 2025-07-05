<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserToken;
use App\Traits\ApiResponse;

class AuthToken
{
   use ApiResponse;
    public function handle(Request $request, Closure $next)
    {   

        $token = $request->header('Authorization');

        if ($token && str_starts_with($token, 'Bearer ')) {
        $token = str_replace('Bearer ', '', $token);
       }

        if(!$token)
        {
         return $this->output(401,('errors.no_token_provided'));
        }
        $record = UserToken::where('token',$token)->where('expires_at','>',now())->first();

        if(!$record){
          return $this->output(401,('errors.invalid_or_expired_token'));
        }
        
        $request->merge(['auth_user'=>$record->user]);

        return $next($request);
    }
}
