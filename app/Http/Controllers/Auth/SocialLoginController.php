<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\SocialAccount;
use App\Models\User;
use Exception;

class SocialLoginController extends Controller
{
    // GitHub の認証ページヘユーザーを転送するためのルート
    public function redirectToProvider(String $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // 認証後に戻るルート
    public function providerCallback(String $provider)
    {
        // エラーならトップページに遷移
        try {
            $social_user = Socialite::with($provider)->user();
        } catch (Exception $e) {
            return redirect('/');
        }

        // name か nickName を userName にする
        if ($social_user->getName()) {
            $user_name = $social_user->getName();
        } else {
            $user_name = $social_user->getNickName();
        }

        // user テーブルに保存
        $auth_user = User::firstOrCreate([
            'email' => $social_user->getEmail(),
            'name' => $user_name
        ]);

        // social account テーブルに保存
        $auth_user->socialAccounts()->firstOrCreate([
            'provider_id' => $social_user->getId(),
            'provider_name' => $provider
        ]);

        // ログイン
        auth()->login($auth_user);

        // トップページに転送
        return redirect()->to('/');
    }
}
