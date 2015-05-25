<?php namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Contracts\Routing\Middleware;

class tokenAuthMiddleware implements Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \App\Exceptions\Unauthorized
     */
	public function handle($request, Closure $next)
	{
        if (
            $request->is('api/user/login')
            || $request->is('api/user') && $request->getMethod() == "POST"
        ) {
            return $next($request);
        }

        $token = $request->header('X-Auth-Token');
        if (!(User::$logged = User::where('api_token', $token)->first())) {
            throw new \App\Exceptions\Unauthorized();
        }
        User::$edited = User::find($request->get('user_id'));
        return $next($request);
	}

}
