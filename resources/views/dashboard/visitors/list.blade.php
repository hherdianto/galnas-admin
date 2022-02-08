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
                            <i class="fa fa-align-justify"></i>{{ __('Visitors') }}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <a href="{{ route('visitors.create') }}"
                                   class="btn btn-primary m-2">{{ __('Add Visitor') }}</a>
                            </div>
                            <br>
                            <table class="table table-responsive-sm table-striped">
                                <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Open book</th>
                                    <th>URL</th>
                                    <th>Aktif</th>
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

@endsection


@section('javascript')
    <script type="text/javascript">
        let tables;

        function deleteEvent(id) {
            $.post({
                url: `{{ url('events') }}/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                }
            })
            .done(function () {
                tables.ajax.reload();
            })
        }

        $(function () {
            tables = $('.table').DataTable({
                ajax: '{{ route('events.fetch') }}',
                processing: true,
                serverSide: true,
                pageLength: 25,
                scrollCollapse: true,
                scrollX: true,
                columns: [
                    {data: 'event_name', name: 'event_name'},
                    {
                        data: 'event_type.type',
                        name: 'eventType.type'
                    },
                    {data: 'location.location_name', name: 'location.location_name'},
                    {
                        data: null,
                        name: 'date_start',
                        render: (data) => {
                            return moment(data.date_start).format('YYYY-MM-DD')
                                + ' - ' + moment(data.date_end).format('YYYY-MM-DD');
                        }
                    },
                    {
                        data: 'open_booking_at',
                        name: 'open_booking_at',
                        render: (data) => {
                            return moment(data).format('YYYY-MM-DD')
                        }
                    },
                    {
                        data: 'url',
                        name: 'url',
                        render: (data) => {
                            return `<a href='${data}' target='_blank'">${data}</a>`
                        },
                        className: 'ellipsis'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        searchable: false,
                        render: (data) => {
                            return `<input type="checkbox" ${data ? 'CHECKED' : ''}>`
                        }
                    },
                    {
                        data: 'id',
                        render: (data) => {
                            return `<a class="btn btn-sm btn-block btn-primary" href="{{ url('/events/') }}/${data}/edit">
<i class="cil-pencil"></i>
</a>`
                            + `<button class="btn btn-sm btn-block btn-danger" onclick="deleteEvent(${data})"><i class="cil-trash"></i></button>`
                        },
                        searchable: false,
                        orderable: false,
                    }
                ],
            })
        })
    </script>
@endsection

