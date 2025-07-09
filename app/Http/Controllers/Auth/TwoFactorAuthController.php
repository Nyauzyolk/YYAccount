<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $google2fa = new Google2FA();
        
        if (!$user->two_factor_enabled) {
            $secret = $google2fa->generateSecretKey();
            $otpUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );
            
            // 使用 bacon/bacon-qr-code 生成二维码
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeImage = $writer->writeString($otpUrl);
            
            return view('auth.2fa.index', compact('secret', 'qrCodeImage'));
        }
        
        return view('auth.2fa.index');
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required']);
        
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($request->secret, $request->code);
        
        if ($valid) {
            auth()->user()->enableTwoFactorAuth($request->secret);

            session(['just_enabled_2fa' => true]);

            return redirect()->route('2fa.index')->with('success', '双重认证已启用');
        }
        
        return back()->with('error', '验证码无效');
    }

    public function disable()
    {
        auth()->user()->disableTwoFactorAuth();
        return redirect()->route('2fa.index')->with('success', '双重认证已禁用');
    }

    public function verify(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('auth.2fa.verify');
        }
    
        $request->validate([
            'code' => 'required|digits:6'
        ]);
    
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(
            auth()->user()->two_factor_secret,
            $request->code
        );
    
        if ($valid) {
            session(['2fa_verified' => true]);
            return redirect()->intended('home')->with('success', '验证成功');
        }
    
        return back()->with('error', '验证码无效');
    }

    public function recovery(Request $request)
    {
        $request->validate(['recovery_code' => 'required']);
        
        $recoveryCodes = json_decode(auth()->user()->two_factor_recovery_codes, true);
        
        if (in_array($request->recovery_code, $recoveryCodes)) {
            $recoveryCodes = array_diff($recoveryCodes, [$request->recovery_code]);
            auth()->user()->update([
                'two_factor_recovery_codes' => json_encode($recoveryCodes)
            ]);
            
            session(['2fa_verified' => true]);
            return redirect()->intended('home')->with('success', '验证成功');;
        }
        
        return back()->with('error', '恢复码无效');
    }
}