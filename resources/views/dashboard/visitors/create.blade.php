@extends('dashboard.base')

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> {{ __('Offline Visit') }}
                            @if(Session::has('errors'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Gagal</strong> {{ Session::get('errors') }}
                                    <button class="close" type="button" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('visitors.store') }}">
                                @if(isset($schedule))
                                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                @endif
                                @csrf
                                <div class="form-group row">
                                    <label>Nama</label>
                                    <input class="form-control" type="text" placeholder="{{ __('Nama Lengkap') }}"
                                           name="full_name" required autofocus value="{{ old('full_name') }}">
                                </div>

                                <div class="form-group row">
                                    <label>Email</label>
                                    <input class="form-control" type="email" placeholder="{{ __('Email') }}"
                                           name="email" required value="{{ old('email') }}">
                                </div>

                                <div class="form-group row">
                                    <label>Phone</label>
                                    <input class="form-control" type="tel" placeholder="{{ __('Phone/HP') }}"
                                           name="phone" required value="{{ old('phone') }}">
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Jenis Kelamin</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label><input type="radio" name="gender" value="1" checked>L</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><input type="radio" name="gender" value="2">P</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Warga Negara</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label><input type="radio" name="indonesian" value="1"
                                                              checked>WNI</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><input type="radio" name="indonesian" value="2">WNA</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label>Usia</label>
                                    <input class="form-control" type="number" placeholder="{{ __('Usia') }}"
                                           name="age" value="{{ old('age') ?: 32 }}" min="6">
                                </div>

                                <div class="form-group row">
                                    <label>
                                        Jml Anggota Tambahan  (max <span
                                            id="max">{{ $schedule->slot - $schedule->visits_count - 1}}</span>)
                                    </label>
                                    <input class="form-control" type="number" placeholder="{{ __('Jml Ang.') }}"
                                           name="groupMember" required value="{{ old('groupMember') ?: 0 }}"
                                           min="0" max="{{ $schedule->slot - $schedule->visits_count - 1 }}">
                                </div>

                                <button class="btn btn-block btn-success" type="submit">{{ __('Add') }}</button>
                                <a href="{{ route('schedules') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a>
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
