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

        .form-control:disabled, .form-control[readonly] {
            background-color: white;
            color: black;
        }

        form.global > .non-global {
            display: none;
        }

        form.dates > .non-dates {
            display: none;
        }

        form.months > .non-month {
            display: none;
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
                            <i class="fa fa-align-justify"></i>{{ __('Visits') }}
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
                            <div class="row">
                                <div class="col">
                                    <form class="form-inline float-right global" id="filter">
                                        <label class="label">Filter:
                                            <select class="form-control mx-2" name="filterType" id="filterType">
                                                <option value="global">Umum</option>
                                                <option value="dates">Tanggal</option>
                                                <option value="months">Bulan</option>
                                            </select>
                                        </label>
                                        <label class="non-dates non-month">
                                            <input class="form-control" name="global" id="filter_global">
                                        </label>
                                        <div class="non-global non-month form-group ">
                                            <label for="from">From <input type="date" id="from" name="from"
                                                                          class="form-control"></label>
                                            <label for="to">to <input type="date" id="to" name="to"
                                                                      class="form-control"></label>
                                        </div>
                                        <label class="non-global non-dates">
                                            <select class="form-control" name="month" id="month">
                                                @for($m = \Carbon\Carbon::parse($to); $m >= \Carbon\Carbon::parse($from); $m = $m->subMonthNoOverflow())
                                                    <option
                                                        @if($m->format("Y-m") == now()->format("Y-m")) selected @endif>
                                                        {{ $m->format("Y-m") }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </label>
                                        <button class="btn btn-info ml-2" type="button" id="submitFilter">Submit
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>J.Kel</th>
                                        <th>Usia</th>
                                        <th>WNI/A</th>
                                        <th>Event</th>
                                        <th>Tgl-Jam</th>
                                        <th>Qty/Pengunjung</th>
                                        <th>Kode</th>
                                        <th>Status</th>
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

    @include('dashboard.shared.visitors.visitor_dialog')
@endsection

@section('javascript')
    <script src="{{ asset('vendors/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script type="text/javascript">
        let tables,
            visitorInfoModal = $('#visitorInfoModal'),
            visitId

        $(function () {
            tables = $('.table').DataTable({
                ajax: {
                    url: `{{ route('visits.fetch') }}${window.location.search}`,
                    data: function (data) {
                        data.filterType = $('#filterType').val()
                        console.log(data.filterType)
                        switch (data.filterType) {
                            case 'dates':
                                data.filterVal = [$('#from').val(), $('#to').val()]
                                break;
                            case 'months':
                                data.filterVal = $('#month').val()
                                break;
                            default:
                                break;
                        }
                    }
                },
                processing: true,
                serverSide: true,
                order: [[3, 'asc']],
                columns: [
                    {data: 'visitor.full_name', name: 'visitor.full_name'},
                    {data: 'visitor.email', name: 'visitor.email'},
                    {
                        data: 'visitor.phone',
                        name: 'visitor.phone',
                        visible: false,
                    },
                    {
                        data: 'visitor.gender',
                        name: 'visitor.gender',
                        visible: false,
                        searchable: false,
                        render: data => {
                            return data === '1' ? 'L' : 'P'
                        }
                    },
                    {
                        data: 'visitor.age',
                        name: 'visitor.age',
                        visible: false
                    },
                    {
                        data: 'visitor.indonesian',
                        name: 'visitor.indonesian',
                        render: data => {
                            return data === '1' ? 'WNI' : 'WNA'
                        }
                    },
                    {
                        data: 'event_schedule',
                        name: 'eventSchedule.event.event_name',
                        render: data => {
                            return data === null ? 'N/A' : data.event.event_name
                        }
                    },
                    {
                        data: 'event_schedule',
                        name: 'eventSchedule.start_time',
                        render: data => {
                            return data === null ? 'N/A' : `${moment(data.start_time).format('YYYY-MM-DD HH:mm')
                            + ' - ' + moment(data.end_time).format('HH:mm')}`
                        },
                        // searchable: false,
                        type: 'date',
                    },
                    {data: 'member_count', name: 'member_count'},
                    {data: 'code', name: 'code'},
                    {
                        data: 'confirmed_at',
                        name: 'confirmed_at',
                        render: data => {
                            return `${data == null ? 'RESERVASI' : 'DATANG'}`
                        },
                        searchable: false,
                    },
                    {
                        data: null,
                        render: (data) => {
                            if (data.confirmed_at)
                                return ''
                            return `<button class="btn btn-sm btn btn-warning" onclick="showConfirmation(${data.id})"><i class="cil-hand-point-up"></i></button>
<button class="btn btn-sm btn btn-danger" onclick="showConfirmation(${data.id})"><i class="cil-remove"></i></button>`
                        },
                        searchable: false,
                        orderable: false,
                    }
                ],
                buttons: [
                    'excelHtml5',
                    'pdfHtml5',
                    'colvis'
                ],
                dom: 'Blrtip',
                lengthMenu: [[25, 50, 100, 250, 500, 1000, -1], [25, 50, 100, 250, 500, 1000, "Semua"]]
            })

            $('#filterType')
                .change(function () {
                    let val = $(this).val()
                    $('#filter').attr('class', `form-inline float-right ${val}`)
                })
                .change()

            $('#submitFilter').click(function () {
                let filterTypeVal = $('#filterType').val(),
                    filterGlobalVal = $('#filter_global').val()
                if (filterTypeVal === 'global' && filterGlobalVal)
                    tables.search(filterGlobalVal).draw()
                else if (filterTypeVal !== 'global')
                    tables.ajax.reload()
            })
        })

        function showConfirmation(id) {
            $.get({
                url: `{{ route('visits') }}/${id}`,
            })
                .done(function (data) {
                    if (data.status === 'success') {
                        visitorInfoModal.modal('show');
                        visitorInfoModal.find('#code').val(data.visit.code)
                        visitorInfoModal.find('#email').val(data.visit.visitor.email)
                        visitorInfoModal.find('#nama').val(data.visit.visitor.full_name)
                        visitorInfoModal.find('#event').val(data.visit.event_schedule.event.event_name)
                        visitorInfoModal.find('#timeSlot')
                            .val(`${moment(data.visit.event_schedule.start_time).format('dddd, YYYY-MM-DD, HH:mm')} - `
                                + `${moment(data.visit.event_schedule.end_time).format('HH:mm')}`)
                        visitorInfoModal.find('#group').val(data.visitCount)
                        if (data.visit.confirmed_at) {
                            visitorInfoModal.find('#code').text(qrCodeMessage)
                            visitorInfoModal.find('#alert').show()
                            visitorInfoModal.find('#confirm').hide()
                        } else {
                            visitorInfoModal.find('#alert').hide()
                            visitorInfoModal.find('#confirm').show()
                            visitId = data.visit.id
                        }
                    } else {
                        Swal.fire("Gagal!", data.message, "error");
                    }
                })
                .fail(function (data) {
                    Swal.fire("Gagal!", data, "error")
                })
                .always(function () {
                    spinner.stop()
                    // html5QrcodeScanner.clear();
                })
        }

        function cancelVisit(id) {

        }

        function confirm() {
            $.post({
                url: `{{ url('visits') }}/${visitId}/confirm`,
                data: {_token: '{{ csrf_token() }}'},
            })
                .done(function (data) {
                    Swal.fire({
                        title: 'Sukses',
                        icon: 'success',
                        text: 'Kunjungan telah dikonfirmasi',
                        confirmButtonText: `OK`,
                    }).then((result) => {
                        tables.ajax.reload()
                    })
                })
        }

        function remove() {
            $.post({
                url: `{{ url('visits') }}/${visitId}/remove`,
                data: {
                    _token: '{{ csrf_token() }}',
                },
            })
                .done(function (data) {
                    Swal.fire({
                        title: 'Sukses',
                        icon: 'success',
                        text: 'Kunjungan telah dihapus',
                        confirmButtonText: `OK`,
                    }).then((result) => {
                        tables.ajax.reload()
                    })
                })
        }
    </script>
@endsection

