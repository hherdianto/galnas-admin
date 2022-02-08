@extends('dashboard.base')

@section('css')
@endsection

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> {{ __('Create user') }}
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card-body">
                            <br>
                            <form method="POST" action="/users">
                                @csrf
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <svg class="c-icon c-icon-sm">
                                          <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-user"></use>
                                      </svg>
                                    </span>
                                    </div>
                                    <input class="form-control" type="text" placeholder="{{ __('Username') }}" name="name"
                                           value="{{ old('name') }}" required autofocus min="5">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-voice-over-record"></i></span>
                                    </div>
                                    <input class="form-control" type="text" placeholder="{{ __('Nama Lengkap') }}"
                                           name="full_name" value="{{ old('full_name') }}" required min="5">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">@</span>
                                    </div>
                                    <input class="form-control" type="email" placeholder="{{ __('E-Mail Address') }}"
                                           name="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-lock-locked"></i></span>
                                    </div>
                                    <input class="form-control" type="password" placeholder="{{ __('Password') }}"
                                           name="password" value="" required>
{{--                                    <span class="input-group-append">--}}
{{--                                        <button type="button" class="btn btn-secondary"><i class="cil-low-vision"></i></button>--}}
{{--                                    </span>--}}
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-lock-unlocked"></i></span>
                                    </div>
                                    <select class="form-control" name="role_id">
                                        @foreach($roles as $role)
                                            <option
                                                value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{--<div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-list"></i></span>
                                    </div>
                                    @foreach($roles as $role)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" value="{{ $role->name }}" class="form-check-input"
                                                   id="role_{{ $role->id }}" name="{{ $role->name }}">
                                            <label class="form-check-label"
                                                   for="role_{{ $role->id }}">{{ $role->name }}</label>
                                        </div>
                                    @endforeach
                                </div>--}}
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-media-stop"></i></span>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" value="1" class="form-check-input" id="user_active" checked name="active">
                                        <label class="form-check-label" for="user_active">Active</label>
                                    </div>
                                </div>
                                <button class="btn btn-block btn-success" type="submit">{{ __('Save') }}</button>
                                <a href="{{ route('users.index') }}"
                                   class="btn btn-block btn-primary">{{ __('Return') }}</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
<script type="text/javascript">
    $(".toggle-password").click(function() {

        // $(this).toggleClass("fa-eye fa-eye-slash");
        let input = $($(this).attr("toggle"));
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>
@endsection
