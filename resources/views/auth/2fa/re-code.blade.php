@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">恢复代码</div>

                <div class="card-body">
                        @if(auth()->user()->two_factor_enabled)
                        <div class="alert mb-4">
                            <p class="mb-0">您的恢复代码（请妥善保管）：</p>
                            <ul class="list-unstyled mb-0">
                                @foreach(json_decode(Auth::user()->two_factor_recovery_codes, true) ?? [] as $code)
                                <li><code>{{ $code }}</code></li>
                                @endforeach
                            </ul>
                        </div>
                        <a type="button" class="btn btn-primary" href="/2fa">返回</a>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection