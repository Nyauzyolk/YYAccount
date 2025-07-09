@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">双重身份验证</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (!auth()->user()->two_factor_enabled)
                        <form method="POST" action="{{ route('2fa.enable') }}">
                            @csrf
                            <input type="hidden" name="secret" value="{{ $secret ?? '' }}">
                            
                            <div class="mb-3">
                                <p>请使用 Google Authenticator 扫描以下二维码：</p>
                                <div class="mb-3 text-center">
                                    {!! $qrCodeImage ?? '' !!}

                                    <p>或者手动输入密钥：</p>
                                    <p><strong>{{ $secret ?? '' }}</strong></p>
                                </div>
                                
                                
                                <label for="code">验证码</label>
                                <input type="text" name="code" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary">启用双重身份验证</button>
                        </form>
                    @else
                        @if(auth()->user()->two_factor_enabled)
                        <!--<div class="alert alert-success mb-4">
                            <h5 class="alert-heading">双重身份验证已启用</h5>
                            <hr>
                            <p class="mb-0">您的恢复代码（请妥善保管）：</p>
                            <ul class="list-unstyled mb-0">
                                @foreach(json_decode(Auth::user()->two_factor_recovery_codes, true) ?? [] as $code)
                                <li><code>{{ $code }}</code></li>
                                @endforeach
                            </ul>
                        </div>-->
                        <form method="POST" action="{{ route('2fa.disable') }}">
                        @csrf
                                <p><a type="button" class="btn btn-warning" href="/2fa/re-code">查看恢复代码</a> <button type="submit" class="btn btn-danger">禁用双重身份验证</button></p>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection