@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Edit') }}: {{ $config->id }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('configs.update', $config->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <div class="col">
                                    <label>Konfigurasi</label>
                                    <input class="form-control" type="text" value="{{ $config->id }}" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Nilai</label>
                                    <input class="form-control" type="text" placeholder="{{ $config->default_value }}"
                                           name="value" value="{{ $config->value }}" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Keterangan</label>
                                    <textarea name="notes" class="form-control" placeholder="Keterangan">{{ $config->notes }}</textarea>
                                </div>
                            </div>

                            <button class="btn btn-block btn-success" type="submit">{{ __('Simpan') }}</button>
                            <a href="{{ route('configs') }}" class="btn btn-block btn-primary">{{ __('Kembali') }}</a>
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
