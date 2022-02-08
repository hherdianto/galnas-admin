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
                            <i class="fa fa-align-justify"></i>{{ __('Kunjungan') }}
                            @if(Session::has('status') && Session::get('status') == 'success')
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Sukses</strong> {{ Session::get('messages') }}
                                    <button class="close" type="button" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th>Slot</th>
                                        <th>Jam</th>
                                        <th>Event</th>
                                        <th>Jml visit</th>
                                        <th></th>
                                    </tr>
                                    </thead>
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
        let tables;
        // moment.tz('Asia/Jakarta')

        $(function () {
            tables = $('.table').DataTable({
                ajax: '{{ route('schedules.fetch') }}/?on_going=1',
                processing: true,
                serverSide: true,
                columns: [
                    {data: 'schedule_name', name: 'schedule_name'},
                    {
                        data: null,
                        name: 'start_time',
                        render: data => {
                            return moment(data.start_time).format('HH:mm')
                                + ' - ' + moment(data.end_time).format('HH:mm');
                        }
                    },
                    {data: 'event.event_name', name: 'event.event_name'},
                    {
                        data: null,
                        name: 'visits_count',
                        render: data => {
                            return `${data.confirmed_count || 0} / ${data.visitor_count || 0} / ${data.slot}`
                        },
                        searchable: false,
                    },
                    {
                        data: 'id',
                        render: (data) => {
                            return `<a class="btn btn-sm btn btn-primary mx-1"
                            data-toggle="tooltip" data-placement="left" title="Tambah kunjungan offline regist"
                            href="{{ route('visitors.create') }}/?schedule_id=${data}"><i class="cil-plus"></i></a>`

                                + `<a class="btn btn-sm btn btn-primary mx-1"
                            data-toggle="tooltip" data-placement="left" title="Pendaftar online"
                                href="{{ route('visits') }}/?schedule_id=${data}&online_only=1"><i class="cil-people"></i></a>`

                                + `<a class="btn btn-sm btn btn-warning mx-1"
                            data-toggle="tooltip" data-placement="left" title="Pendaftar offline"
                                href="{{ route('visits') }}/?schedule_id=${data}&offline_only=1"><i class="cil-people"></i></a>`
                        },
                        searchable: false,
                        orderable: false,
                    }
                ],
            })
        })
    </script>
@endsection

