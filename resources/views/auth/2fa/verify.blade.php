@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">双重身份验证</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('2fa.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="code">请输入验证码</label>
                            <p></p>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">验证</button>
                    </form>

                    <hr>

                    <form method="POST" action="{{ route('2fa.recovery') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="recovery_code">使用恢复码</label>
                            <p></p>
                            <input type="text" name="recovery_code" class="form-control" required>
                        </div>

                        <button type="submit" name="recode" class="btn btn-secondary">使用恢复码</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection