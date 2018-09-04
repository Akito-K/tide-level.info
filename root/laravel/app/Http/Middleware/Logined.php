<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class Logined
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        // ログイン記録
        User::where('hashed_id', $user->hashed_id)->update([
            'last_logined_at' => new \Datetime(),
        ]);
        /*
                // 未読メッセージを取得
                $unreads = MessageUnopened::getUnreads($user->user_id);
                // Request に混ぜて Controller へ送る
                $request->merge([
                    'middleware_unreads' => $unreads,
                ]);
        */
        return $next($request);
    }
}
