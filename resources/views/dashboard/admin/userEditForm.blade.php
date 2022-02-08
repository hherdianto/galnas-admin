@extends('dashboard.base')

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> {{ __('Edit') }} {{ $user->name }}
                            <div class="row">
                                <div class="col-12">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <br>
                            <form method="POST" action="/users/{{ $user->id }}">
                                @csrf
                                @method('PUT')
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <svg class="c-icon c-icon-sm">
                                          <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-user"></use>
                                      </svg>
                                    </span>
                                    </div>
                                    <input class="form-control" type="text" placeholder="{{ __('Name') }}" name="name"
                                           value="{{ $user->name }}" disabled min="5">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-voice-over-record"></i></span>
                                    </div>
                                    <input class="form-control" type="text" placeholder="{{ __('Nama Lengkap') }}"
                                           name="full_name" value="{{ $user->full_name }}" required autofocus min="5">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">@</span>
                                    </div>
                                    <input class="form-control" type="text" placeholder="{{ __('E-Mail Address') }}"
                                           name="email" value="{{ $user->email }}" disabled>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-lock-locked"></i></span>
                                    </div>
                                    <input class="form-control" type="password" placeholder="{{ __('Password') }}"
                                           name="password" value="" min="5">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-lock-unlocked"></i></span>
                                    </div>
                                    <select class="form-control" name="role_id">
                                        @foreach($roles as $role)
                                            <option
                                                value="{{ $role->id }}" {{ $role->id == $user->role_id ? 'SELECTED' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{--<div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="cil-list"></i></span>
                                    </div>
                                    @foreach($roles as $role)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" value="{{ $role->name }}" class="form-check-input" name="{{{ $role->name }}}"
                                                   {{ in_array($role->name, explode(',', $user->menuroles)) ? 'checked' : '' }} id="role_{{ $role->id }}">
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
                                        <input type="checkbox" value="1" class="form-check-input" name="active"
                                               {{ $user->active ? 'checked' : '' }} id="user_active">
                                        <label class="form-check-label"
                                               for="user_active">Active</label>
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

@endsection
