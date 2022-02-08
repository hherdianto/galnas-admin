@extends('dashboard.base')

@section('css')
    <style>
        .table {
            white-space: nowrap;
        }
        .ellipsis {
            max-width: 7em;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i>{{ __('Konfigurasi') }}
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th>Nama Config</th>
                                        <th>Nilai</th>
                                        <th>Keterangan</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($configs AS $config)
                                        <tr>
                                            <td>
                                                {{ $config->id }}
                                            </td>
                                            <td>
                                                {{ $config->value }}
                                                <a type="button" class="btn btn-sm btn-primary" href="{{ route('configs.edit', $config->id) }}">
                                                    <i class="cil-pencil"></i>
                                                </a>
                                            </td>
                                            <td>
                                                {{ $config->notes }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('javascript')
    <script type="text/javascript">
    </script>
@endsection

