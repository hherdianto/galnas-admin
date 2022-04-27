@extends('dashboard.base')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body pb-0">
                            {{--<div class="btn-group float-right">
                              <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="c-icon">
                                  <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                                </svg>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right">
                                  <a class="dropdown-item" href="#">Action</a>
                                  <a class="dropdown-item" href="#">Another action</a>
                                  <a class="dropdown-item" href="#">Something else here</a>
                              </div>
                            </div>--}}
                            <div class="text-value-lg">{{ number_format($online, 0, ',', '') }}</div>
                            <div>Pendaftaran online</div>
                        </div>
                        <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-chart-online" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-info">
                        <div class="card-body pb-0">
                            {{--<button class="btn btn-transparent p-0 float-right" type="button">
                              <svg class="c-icon">
                                <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-location-pin"></use>
                              </svg>
                            </button>--}}
                            <div class="text-value-lg">{{ number_format($offline, 0, ',', '.') }}</div>
                            <div>Pendaftar offline</div>
                        </div>
                        <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-chart-offline" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body pb-0">
                            <div class="btn-group float-right">
                                {{--<button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <svg class="c-icon">
                                    <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                                  </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a class="dropdown-item" href="#">Something else here</a></div>--}}
                            </div>
                            <div class="text-value-lg">{{ number_format($confirmed, 0, ',', '.') }}</div>
                            <div>Kunjungan real</div>
                        </div>
                        <div class="c-chart-wrapper mt-3" style="height:70px;">
                            <canvas class="chart" id="card-chart-confirmed" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body pb-0">
                            {{--<div class="btn-group float-right">
                              <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="c-icon">
                                  <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                                </svg>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Action</a><a class="dropdown-item" href="#">Another action</a><a class="dropdown-item" href="#">Something else here</a></div>
                            </div>--}}
                            <div class="text-value-lg">{{ number_format($uniqueVisitor, 0, ',', '.') }}</div>
                            <div>Pengunjung terdaftar</div>
                        </div>
                        <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-chart-register" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Pendaftaran Kunjungan</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table responsive table-striped" id="visitsTable">
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
                <!-- /.col-->
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Kunjungan per Event</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table responsive table-striped" id="visitPerEventTable">
                                    <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Tanggal</th>
                                        <th>R.Online</th>
                                        <th>R.Offline</th>
                                        <th>K.Online</th>
                                        <th>Total</th>
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

    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/coreui-chartjs.bundle.js') }}"></script>
    <script src="{{ asset('js/main.js') }}" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>

    <script type="text/javascript">
        let visitorInfoModal = $('#visitorInfoModal')

        $(function () {
            $.get({
                url: '{{ route('dashboard.onlinePerDay') }}'
            })
                .done(function (data) {
                    new Chart(document.getElementById('card-chart-online'), {
                        type: 'line',
                        data: {
                            labels: data.map(data => data.evDate),
                            datasets: [
                                {
                                    label: 'Kunjungan Online',
                                    backgroundColor: 'transparent',
                                    borderColor: 'rgba(255,255,255,.55)',
                                    pointBackgroundColor: coreui.Utils.getStyle('--primary'),
                                    data: data.map(data => data.total)
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{
                                    gridLines: {
                                        color: 'transparent',
                                        zeroLineColor: 'transparent'
                                    },
                                    ticks: {
                                        fontSize: 2,
                                        fontColor: 'transparent'
                                    }
                                }],
                                yAxes: [{
                                    display: false,
                                    ticks: {
                                        display: false,
                                    }
                                }]
                            },
                            elements: {
                                line: {
                                    borderWidth: 1
                                },
                                point: {
                                    radius: 4,
                                    hitRadius: 10,
                                    hoverRadius: 4
                                }
                            }
                        }
                    })
                })

            $.get({
                url: '{{ route('dashboard.offlinePerDay') }}'
            })
                .done(function (data) {
                    new Chart(document.getElementById('card-chart-offline'), {
                        type: 'line',
                        data: {
                            labels: data.map(data => data.evDate),
                            datasets: [
                                {
                                    label: 'Kunjungan Offline',
                                    backgroundColor: 'transparent',
                                    borderColor: 'rgba(255,255,255,.55)',
                                    pointBackgroundColor: coreui.Utils.getStyle('--primary'),
                                    data: data.map(data => data.total)
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{
                                    gridLines: {
                                        color: 'transparent',
                                        zeroLineColor: 'transparent'
                                    },
                                    ticks: {
                                        fontSize: 2,
                                        fontColor: 'transparent'
                                    }
                                }],
                                yAxes: [{
                                    display: false,
                                    ticks: {
                                        display: false,
                                    }
                                }]
                            },
                            elements: {
                                line: {
                                    borderWidth: 1
                                },
                                point: {
                                    radius: 4,
                                    hitRadius: 10,
                                    hoverRadius: 4
                                }
                            }
                        }
                    })
                })

            $.get({
                url: '{{ route('dashboard.confirmedPerDay') }}'
            })
                .done(function (data) {
                    new Chart(document.getElementById('card-chart-confirmed'), {
                        type: 'line',
                        data: {
                            labels: data.map(data => data.evDate),
                            datasets: [
                                {
                                    label: 'Kunjungan Real',
                                    backgroundColor: 'transparent',
                                    borderColor: 'rgba(255,255,255,.55)',
                                    pointBackgroundColor: coreui.Utils.getStyle('--primary'),
                                    data: data.map(data => data.total)
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{
                                    gridLines: {
                                        color: 'transparent',
                                        zeroLineColor: 'transparent'
                                    },
                                    ticks: {
                                        fontSize: 2,
                                        fontColor: 'transparent'
                                    }
                                }],
                                yAxes: [{
                                    display: false,
                                    ticks: {
                                        display: false,
                                    }
                                }]
                            },
                            elements: {
                                line: {
                                    borderWidth: 1
                                },
                                point: {
                                    radius: 4,
                                    hitRadius: 10,
                                    hoverRadius: 4
                                }
                            }
                        }
                    })
                })

            $.get({
                url: '{{ route('dashboard.registerPerDay') }}'
            })
                .done(function (data) {
                    new Chart(document.getElementById('card-chart-register'), {
                        type: 'line',
                        data: {
                            labels: data.map(data => data.regDate),
                            datasets: [
                                {
                                    label: 'Pendaftaran',
                                    backgroundColor: 'transparent',
                                    borderColor: 'rgba(255,255,255,.55)',
                                    pointBackgroundColor: coreui.Utils.getStyle('--primary'),
                                    data: data.map(data => data.total)
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{
                                    gridLines: {
                                        color: 'transparent',
                                        zeroLineColor: 'transparent'
                                    },
                                    ticks: {
                                        fontSize: 2,
                                        fontColor: 'transparent'
                                    }
                                }],
                                yAxes: [{
                                    display: false,
                                    ticks: {
                                        display: false,
                                    }
                                }]
                            },
                            elements: {
                                line: {
                                    borderWidth: 1
                                },
                                point: {
                                    radius: 4,
                                    hitRadius: 10,
                                    hoverRadius: 4
                                }
                            }
                        }
                    })
                })

            $('#visitsTable').DataTable({
                ajax: `{{ route('visits.fetch') }}`,
                processing: true,
                serverSide: true,
                order: [[3, 'desc']],
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
                            return (data === '1' ? 'L' : 'P')
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
                    {data: 'event_schedule.event.event_name', name: 'eventSchedule.event.event_name'},
                    {
                        data: 'event_schedule',
                        name: 'eventSchedule.start_time',
                        render: data => {
                            return `${moment(data.start_time).format('YYYY-MM-DD HH:mm')
                            + ' - ' + moment(data.end_time).format('HH:mm')}`
                        }
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
                            return `<button class="btn btn-sm btn btn-primary" onclick="showConfirmation(${data.id})"><i class="cil-check"></i></button>`
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
                dom: 'Blfrtip',
                lengthMenu: [[25, 50, 100, 250, 500, 1000, -1], [25, 50, 100, 250, 500, 1000, "Semua"]]
            })

            $('#visitPerEventTable').DataTable({
                ajax: `{{ route('dashboard.visitPerEvent') }}`,
                processing: true,
                serverSide: true,
                order: [[1, 'desc']],
                columns: [
                    {data: 'event_name', name: 'event_name'},
                    {
                        data: null,
                        name: 'date_start',
                        render: data => {
                            return `${moment(data.date_start).format('Y-MM-DD')} - ${moment(data.date_end).format('Y-MM-DD')}`
                        }
                    },
                    {
                        data: 'totalOnline',
                        name: 'totalOnline',
                        searchable: false,
                        className: 'text-right',
                    },
                    {
                        data: 'totalOffline',
                        name: 'totalOffline',
                        searchable: false,
                        className: 'text-right',
                    },
                    {
                        data: 'totalOnlineConfirmed',
                        name: 'totalOnlineConfirmed',
                        searchable: false,
                        className: 'text-right',
                    },
                    {
                        data: 'totalConfirmed',
                        name: 'totalConfirmed',
                        searchable: false,
                        className: 'text-right',
                    },
                ],
                buttons: [
                    'excelHtml5',
                    'pdfHtml5',
                ],
                dom: 'Blfrtip',
                lengthMenu: [[25, 50, 100, 250, 500, 1000, -1], [25, 50, 100, 250, 500, 1000, "Semua"]]
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
    </script>
@endsection
