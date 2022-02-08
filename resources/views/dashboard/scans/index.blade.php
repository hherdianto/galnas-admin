@extends('dashboard.base')

@section('css')
    <style>
        .form-control:disabled, .form-control[readonly] {
            background-color: white;
            color: black;
        }
    </style>
@endsection

@section('content')
    <div id="readerWrapper">
        <div id="reader" style="width: 500px; margin: auto"></div>

        <div id="manualInput" style="max-width: 500px; margin: auto; padding-top: 50px">
            <div style="margin: auto; width: max-content">
                <label>Code<input name="code" id="code" onfocus="$(this).select()" onchange=""></label>
                <button class="btn btn-primary" type="button" onclick="onScanSuccess($('#code').val(), true)">Submit</button>
            </div>
        </div>
    </div>

    @include('dashboard.shared.visitors.visitor_dialog')
@endsection

@section('javascript')
    <script src="//cdnjs.cloudflare.com/ajax/libs/html5-qrcode/1.2.1/html5-qrcode.min.js"
            integrity="sha512-zeTGtLbWu1N3jh5NbDjakPt+Ia6tSlvcHt5m05OzTmHx76nrolSKGajF1+DrO9V8xcF/7yM4Q9V3VvsP0TIffw=="
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>
    <script type="text/javascript">
        let visitorInfoModal = $('#visitorInfoModal')
        let spinner = new Spinner().spin()
        let visitId
        let lastScan = ''

        moment.locale('id')

        $('#code').val(function () {
            return this.value.toUpperCase()
        })

        function confirm() {
                $.post({
                    url: `{{ url('visits') }}/${visitId}/confirm`,
                    data: {_token: '{{ csrf_token() }}'},
                })
                    .done(function (data) {
                        Swal.fire("Sukses!", "Visit telah di konfirmasi", "success");
                    })
        }

        function onScanSuccess(qrCodeMessage, byPassLastScan = false) {
            // handle on success condition with the decoded message
            if ((lastScan !== qrCodeMessage || byPassLastScan) && !visitorInfoModal.hasClass('show')) {
                lastScan = qrCodeMessage
                $('#readerWrapper').append(spinner.el)
                $.post({
                    url: `{{ route('scans.post') }}`,
                    data: {
                        _token: '{{ csrf_token() }}',
                        qrCodeMessage: qrCodeMessage,
                    }
                })
                    .done(function (data) {
                        if (data.status === 'success') {
                            let date = moment(data.visit.event_schedule.start_time.substring(10)),
                                sTime = moment(data.visit.event_schedule.start_time)
                            visitorInfoModal.modal('show')
                            visitorInfoModal.find('#code').val(data.visit.code)
                            visitorInfoModal.find('#email').val(data.visit.visitor.email)
                            visitorInfoModal.find('#nama').val(data.visit.visitor.full_name)
                            visitorInfoModal.find('#event').val(data.visit.event_schedule.event.event_name)
                            visitorInfoModal.find('#timeSlot')
                                .val(`${moment(data.visit.event_schedule.start_time).format('dddd, YYYY-MM-DD, HH:mm')} - `
                                    + `${moment(data.visit.event_schedule.end_time).format('HH:mm')}`)
                            visitorInfoModal.find('#group').val(data.visit.member_count)
                            if (data.visit.confirmed_at) {
                                visitorInfoModal.find('#alert').show()
                                visitorInfoModal.find('#code').text(qrCodeMessage)
                                visitorInfoModal.find('#confirm').hide()
                            } else if(moment(data.visit.event_schedule.start_time).subtract({{ $earliestLimit }}, 'minutes').isAfter(moment())
                                || moment(data.visit.event_schedule.end_time).isBefore(moment())) {
                                visitorInfoModal.find('#scheduleAlert').show()
                                // visitorInfoModal.find('#scheduleAlert').addClass('show')
                                visitorInfoModal.find('#alertDate')
                                    .text(moment(data.visit.event_schedule.start_time).format('Y-MM-DD'))
                                visitorInfoModal.find('#alertTime')
                                    .text(`${moment(data.visit.event_schedule.start_time).format('HH:mm')}
                                    - ${moment(data.visit.event_schedule.end_time).format('HH:mm')}`)
                                visitorInfoModal.find('#confirm').hide()
                            } else {
                                visitorInfoModal.find('#alert').hide()
                                visitorInfoModal.find('#scheduleAlert').hide()
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
            $('#code').select()
        }

        function onScanError(errorMessage) {
            // handle on error condition, with error message
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", {fps: 6, qrbox: 250})
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    </script>
@endsection
