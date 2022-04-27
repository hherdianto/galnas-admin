@extends('dashboard.base')

@section('css')
    <link rel="stylesheet" href="{{ asset('/vendors/jquery-ui-1.12.1.custom/jquery-ui.min.css') }}"/>
    <style>
        .ui-highlight {
            background: yellow !important;
            border-color: yellow !important;
            color: yellow !important;
        }

        #add_date {
            float: right;
        }
    </style>
@endsection

@section('content')
    <?php /** @var \App\Models\Event $event */ ?>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> {{ __('Create Event') }}
                        </div>
                        <div class="card-body" id="app">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#formEventContent" role="tab"
                                       aria-controls="formEventContent">
                                        Event
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ isset($event->id) ? '' : 'disabled' }}" data-toggle="tab"
                                       href="#timeSlotsContent" role="tab" aria-controls="formEventContent"
                                       id="timeSlotTab">
                                        Time Slots
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="formEventContent" class="tab-pane active" role="tabpanel">
                                    <form method="POST" action="{{ route('events.store') }}" id="formEvent">
                                        @csrf
                                        <div class="form-group row">
                                            <label>Nama</label>
                                            <input class="form-control" type="text" placeholder="{{ __('Nama') }}"
                                                   name="event_name" required autofocus
                                                   value="{{ $event->event_name }}">
                                        </div>

                                        <div class="form-group row">
                                            <label>Tipe</label>
                                            <select class="form-control" name="event_type_id">
                                                @foreach($eventTypes as $eventType)
                                                    <option value="{{ $eventType->id }}"
                                                        {{ $event->event_type_id == $eventType->id ? 'SELECTED' : '' }}>
                                                        {{ $eventType->type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group row">
                                            <label>Lokasi</label>
                                            <select class="form-control" name="location_id">
                                                @foreach($locations as /** @var \App\Models\Location $location */$location)
                                                    <option value="{{ $location->id }}"
                                                        {{ $event->location_id == $location->id ? 'SELECTED' : '' }}>
                                                        {{ $location->location_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-12">Tgl Mulai
                                                <button type="button" class="btn btn-primary" id="add_date"
                                                        data-toggle="modal" data-target="#addDateModal">
                                                    Tambah Tanggal
                                                </button>
                                            </label>
                                            <div class="col-sm-5">
                                                <input type="date" class="form-control" name="date_start" required
                                                       value="{{ optional($event->date_start)->format('Y-m-d') }}"/>
                                            </div>
                                            <div class="col-sm-1">
                                                <label>-</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="date" class="form-control" name="date_end" required
                                                       value="{{ optional($event->date_end)->format('Y-m-d') }}"/>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label>Open Booking</label>
                                            <input type="date" class="form-control" name="open_booking_at" required
                                                   value="{{ optional($event->open_booking_at)->format('Y-m-d') ?: today()->format('Y-m-d') }}"/>
                                        </div>

                                        <div class="form-group row">
                                            <label>URL</label>
                                            <input type="url" class="form-control" name="url"
                                                   value="{{ $event->url }}"/>
                                        </div>

                                        <div class="form-group row">
                                            <label>Deskripsi</label>
                                            <textarea class="form-control" id="textarea-input" name="notes" rows="9"
                                                      placeholder="{{ __('Deskripsi..') }}">{{ $event->notes }}</textarea>
                                        </div>

                                        <div class="form-group row">
                                            <label>
                                                Aktif
                                                <input type="checkbox" id="is_active" value="1"
                                                       name="is_active" {{ $event->is_active ? 'checked' : '' }}>
                                            </label>
                                        </div>
                                    </form>

                                    <div class="form-group row">
                                        <label>
                                            Gambar
                                            <input type="file" id="image"
                                                   value="{{ optional($event->images->first)->image }}"
                                                   name="image">
                                        </label>
                                    </div>

                                    <button class="btn btn-block btn-success" onclick="post(false)"
                                            type="button">
                                        {{ __('Save & Set Time Slot') }}</button>
                                    {{--                                    <button class="btn btn-block btn-success" onclick="post(true)"--}}
                                    {{--                                            type="button">{{ __('Save & Return') }}</button>--}}
                                    <a href="{{ route('events') }}"
                                       class="btn btn-block btn-primary">{{ __('Return') }}</a>
                                </div>
                                <div id="timeSlotsContent" class="tab-pane" role="tabpanel">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Tgl: <span class="date-range">
                                                {{ optional($event->date_start)->format('Y-m-d') }}
                                                -
                                                {{ optional($event->date_end)->format('Y-m-d') }}
                                            </span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>
                                                Tanggal: <span id="selectedDateView">{{ now()->format('Y-m-d') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4" id="dow">
                                            {{--@for($i = 0; $i < 7; $i++)
                                                <label>
                                                    {{ $dows[$i] }}
                                                    <input type="checkbox" name="dow{{ $i }}" data-dow="{{ $i }}"
                                                        {{ $event->dayOfWeeks->where('day_of_week', '=', $i) ? 'checked' : ''}}>
                                                </label>
                                            @endfor--}}
                                            <label class="form-check-label">
                                                Minggu
                                                <input type="checkbox" name="dow0" data-dow="0"
                                                       class="form-check-inline"
                                                    {{ $event->dayOfWeeks->where('day_of_week', '=', 0) ? 'checked' : ''}}>
                                            </label>
                                            <label class="form-check-label">
                                                Senin
                                                <input type="checkbox" name="dow1" data-dow="1"
                                                       class="form-check-inline"
                                                    {{ $event->dayOfWeeks->where('day_of_week', '=', 1) ? 'checked' : ''}}>
                                            </label>
                                            <label class="form-check-label">
                                                Selasa
                                                <input type="checkbox" name="dow2" data-dow="2"
                                                       class="form-check-inline"
                                                    {{ $event->dayOfWeeks->where('day_of_week', '=', 2) ? 'checked' : ''}}>
                                            </label>
                                            <label class="form-check-label">
                                                Rabu
                                                <input type="checkbox" name="dow3" data-dow="3"
                                                       class="form-check-inline"
                                                    {{ $event->dayOfWeeks->where('day_of_week', '=', 3) ? 'checked' : ''}}>
                                            </label>
                                            <label class="form-check-label">
                                                Kamis
                                                <input type="checkbox" name="dow4" data-dow="4"
                                                       class="form-check-inline"
                                                    {{ $event->dayOfWeeks->where('day_of_week', '=', 4) ? 'checked' : ''}}>
                                            </label>
                                            <label class="form-check-label">
                                                Jumat
                                                <input type="checkbox" name="dow5" data-dow="5"
                                                       class="form-check-inline"
                                                    {{ $event->dayOfWeeks->where('day_of_week', '=', 5) ? 'checked' : ''}}>
                                            </label>
                                            <label class="form-check-label">
                                                Sabtu
                                                <input type="checkbox" name="dow6" data-dow="6"
                                                       class="form-check-inline"
                                                    {{ $event->dayOfWeeks->where('day_of_week', '=', 6) ? 'checked' : ''}}>
                                            </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <button class="btn btn-primary m-2" onclick="formAdd()">
                                                {{ __('Tambah Slot') }}
                                            </button>
                                            {{--<button class="btn btn-primary m-2" onclick="saveSlot()">
                                                {{ __('Simpan') }}
                                            </button>--}}
                                            <button class="btn btn-primary m-2" onclick="saveSlot('dow')" id="repeat">
                                                {{ __('Simpan utk tiap hari') }}
                                            </button>
                                            <button class="btn btn-primary m-2" onclick="saveSlot('all')">
                                                {{ __('Simpan utk tiap hari') }}
                                            </button>
                                            <label class="form-check-label">
                                                <input type="checkbox" onclick="toggleDate()"
                                                       id="toggle-date">{{ __('Aktif') }}
                                            </label>
                                            {{--<button class="btn btn-danger m-2" onclick="deactivateDate()">
                                                <i class="cil-trash"></i>
                                                {{ __('Disable tgl ini') }}
                                            </button>--}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div id="calendarEvents"></div>
                                        </div>
                                        <div class="col-sm-8">
                                            <table class="table table-responsive-sm table-striped text-center"
                                                   id="timeSlotTable" style="width: 100%; white-space: nowrap">
                                                <thead>
                                                <tr>
                                                    <th>Session</th>
                                                    <th>Jam</th>
                                                    <th>Quota</th>
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
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addDateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Event Date</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="form-editSlotTime">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="oldValue">Change from:</label>
                                    <input id="oldValue" class="form-control" readonly
                                           value="{{ optional($event->date_start)->format('m/d/Y') }} - {{ optional($event->date_end)->format('m/d/Y') }}">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="start_date">To</label>
                                    <input id="start_date" value="{{ optional($event->date_start)->format('m/d/Y') }}"
                                           readonly class="form-control">
                                    <label class="control-label" for="end_date"> until </label>
                                    <input type="date" id="end_date" name="end_date" class="form-control"
                                           value="{{ optional($event->date_end)->format('Y-m-d') }}"
                                           min="{{ optional($event->date_end)->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="postAddDate()">
                        Save
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSlotTimeDialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Event</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="form-editSlotTime">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="oldValue">Session Name</label>
                                    <input id="schedule_name" class="form-control" name="schedule_name"
                                           placeholder="Session #" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="start_time">Time</label>
                                    <input type="time" id="start_time" name="start_time" step="1800" value="09:00">
                                    <label class="control-label" for="end_date"> - </label>
                                    <input type="time" id="end_time" name="end_time" step="1800" value="10:00">
                                    <div class="help-block small">AM: setelah tengah malam, PM: setelah siang</div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="slot">Quota</label>
                                    <input type="number" name="slot" id="slot" min="0" value="20" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="is_active">Active</label>
                                    <input type="checkbox" name="is_active" id="is_active" checked>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="saveTemplate()">
                        Save
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('vendors/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="//unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        let timeSlotTable,
            dows = [
                'Minggu',
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu',
            ],
            calendarEvents = $('#calendarEvents'),
            selectedIndex = -1,
            modal = $('#editSlotTimeDialog'),
            event = @if($event->id)
                {!!  $event->toJson() !!}
                @else
            {}
            @endif,
            dateSchedule = {!!
    $event->schedules()->where(['is_active' => 1])->groupByRaw('dateSchedule')->selectRaw('DATE(start_time) dateSchedule')
    ->get()->pluck('dateSchedule')->toJson()
     !!},
            editTimeSlotModal = new coreui.Modal(document.getElementById('editSlotTimeDialog'), {
                keyboard: false
            }),
            minDate = new Date('{{ now()->max($event->date_start)->format('Y-m-d') }}'),
            maxDate = new Date('{{ now()->max($event->date_end)->format('Y-m-d') }}')

        function selectDate(selectedDate) {
            $('#selectedDateView').text(moment(selectedDate).format('YYYY-MM-DD'))
            timeSlotTable.ajax.url(`{{ url('events') }}/${event.id}/slots?selectedDate=` + moment(selectedDate).format('YYYY-MM-DD')).load()
            $('#repeat').text(`Simpan utk tiap hari ${moment(selectedDate).format('dddd')}`)
            $('#toggle-date').prop('checked', dateSchedule.includes(moment(selectedDate).format('YYYY-MM-DD')))
        }

        calendarEvents.datepicker({
            minDate: minDate,
            maxDate: maxDate,
            onSelect: function (selectedDate, i) {
                /*$('#selectedDateView').text(moment(selectedDate).format('YYYY-MM-DD'))
                timeSlotTable.ajax.url(`{{ url('events') }}/${event.id}/slots?selectedDate=` + moment(selectedDate).format('YYYY-MM-DD')).load()
                $('#repeat').text(`Simpan utk tiap hari ${moment(selectedDate).format('dddd')}`)
                $('#toggle-date').prop('checked')*/
                selectDate(selectedDate)
            },
        })

        function TimeSlot(schedule_name, start_time, end_time, slot = 20, is_active = 1) {
            this.schedule_name = schedule_name;
            this.start_time = start_time;
            this.end_time = end_time;
            this.slot = slot;
            this.is_active = is_active;
        }

        function toggleDow() {
            calendarEvents.datepicker("option", "beforeShowDay", function (date) {
                let day = date.getDay()
                if ($(`input[name=dow${day}]`).is(':checked') && minDate <= date && maxDate >= date) {
                    if (!dateSchedule.includes(moment(date).format('YYYY-MM-DD')))
                        return [true, 'ui-highlight', 'inactive']
                    else return [true]
                } else
                    return false
            })
        }

        function post(is_returning = false) {
            let imageInput = $('#image')
            $.post({
                url: @if($event->id)
                `{{ route('events.update', $event->id) }}`
                    @else
                    `{{ route('events.store') }}`
                @endif
                ,
                method: '{{ $event->id ? 'PUT' : 'POST' }}',
                data: $('#formEvent').serialize()
            })
                .done(function (response) {
                    if (imageInput.val()) {
                        let fd = new FormData()
                        let image = imageInput[0].files[0]
                        fd.append('image', image)
                        fd.append('_token', '{{ csrf_token() }}')
                        $.post({
                            url: `{{ url('/events/') }}/${event.id}/images`,
                            data: fd,
                            contentType: false,
                            processData: false,
                        })
                    }
                    if (is_returning)
                        window.location = '{{ url('events') }}'
                    else {
                        $('a[href="#timeSlotsContent"]').tab('show')
                        event = response.event
                        $('.date-range')
                            .text(`${moment(response.event.date_start).format('YYYY-MM-DD')}
                            - ${moment(response.event.date_end).format('YYYY-MM-DD')}`)
                        calendarEvents.datepicker("option", "minDate", new Date(response.event.date_start))
                        calendarEvents.datepicker("option", "maxDate", new Date(response.event.date_end))
                    }
                })
                .fail(function (data) {
                    alert(data)
                })
        }

        function saveSlot(repeat) {
            $.post({
                url: `{{ url('/events/') }}/${event.id}/slots`,
                data: {
                    _token: '{{ csrf_token() }}',
                    date: moment(calendarEvents.val()).format('YYYY-MM-DD'),
                    repeat: repeat
                }
            })
                .done(function (response) {
                    alert('Data berhasil disimpan')
                })
                .fail(function (xhr, status, error) {
                    alert(xhr.responseJSON.messages || error)
                })
        }

        function edit(row, id) {
            editTimeSlotModal.show()
            selectedIndex = row
            let timeSlot = timeSlotTable.row(row).data()
            modal.find('[name=schedule_name]').val(timeSlot.schedule_name).data('id', id)
            modal.find('[name=start_time]').val(timeSlot.start_time.length === 5 ? timeSlot.start_time : moment(timeSlot.start_time).format('HH:mm'))
            modal.find('[name=end_time]').val(timeSlot.end_time.length === 5 ? timeSlot.end_time : moment(timeSlot.end_time).format('HH:mm'))
            modal.find('[name=slot]').val(timeSlot.slot)
            modal.find('[name=is_active]').val(timeSlot.is_active).prop('checked', parseInt(timeSlot.is_active) === 1)
        }

        function formAdd() {
            selectedIndex = -1;
            editTimeSlotModal.show();
            modal.find('[name=schedule_name]').val('')
            modal.find('[name=start_time]').val('09:00')
            modal.find('[name=end_time]').val('10:00')
            modal.find('[name=slot]').val(20)
            modal.find('[name=is_active]').val(1).prop('checked', true)
            $('#schedule_name').data('id', null)
        }

        function deleteRow(row, id, schedule_name, start_time, end_time) {
            swal({
                title: 'Konfirmasi',
                text: `Hapus slot ${schedule_name} ${moment(start_time).format('HH:mm')} - ${moment(end_time).format('HH:mm')}?`,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((confirm) => {
                    if (confirm) {
                        $.post({
                            url: `{{ url('schedules') }}/${id}`,
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE',
                            }
                        })
                            .done(({status}) => {
                                console.log(status)
                                if (status === 'success') {
                                    timeSlotTable.row(row).remove()
                                    timeSlotTable.draw(false)
                                    swal(`Slot ${schedule_name} ${moment(start_time).format('HH:mm')} - ${moment(end_time).format('HH:mm')}
                                    berhasil di Hapus. `, {
                                        icon: "success",
                                    })
                                }
                            })
                            .fail(function (data) {
                                console.log('fail')
                                alert(data)
                                console.log(data)
                            })
                    }
                })
        }

        function saveTemplate(id = null) {
            let schedule_name = $('#schedule_name')
            if (schedule_name.val() === '') {
                $('#error_name').text('Nama Session harus diisi')
                alert('Nama Session harus diisi')
                return false
            } else {
                $('#error_name').text('')
            }
            $.post({
                url: `{{ url('events') }}/${event.id}/slot`,
                data: {
                    _token: '{{ csrf_token() }}',
                    id: schedule_name.data('id'),
                    schedule_name: schedule_name.val(),
                    date: moment(calendarEvents.val()).format('YYYY-MM-DD'),
                    start_time: $('#start_time').val(),
                    end_time: $('#end_time').val(),
                    slot: $('#slot').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0,
                }
            })
                .done(response => {
                    timeSlotTable.ajax.reload()
                    alert('Data berhasil disimpan')
                })
                .fail(function (data) {
                    alert(data)
                    console.log(data)
                })
            // let data = new TimeSlot()
            // modal.find('form').serializeArray().map(item => {
            //     console.log(item)
            //     data[item.name] = item.value
            // })
            // console.log(data)
            // if (selectedIndex > -1)
            //     timeSlotTable.row(selectedIndex).data(data)
            // else timeSlotTable.row.add(data).draw(false)
        }

        function postAddDate() {
            $.post({
                url: `{{ url('events') }}/${event.id}/addDate`,
                data: {
                    _token: '{{ csrf_token() }}',
                    end_date: $('#end_date').val(),
                }
            })
                .done(response => {
                    swal('Sukses', 'Tanggal telah berhasil ditambahkan', 'success')
                        .then(_ => location.reload())
                })
                .fail(function (data) {
                    alert(data.responseJson.message || data.statusText)
                    console.log(data)
                })
        }

        function toggleDate() {
            let toggle = $('#toggle-date').is(':checked')
            swal({
                title: 'Konfirmasi',
                text: `${toggle ? 'Aktifkan' : 'Disable'} slot tgl ${moment(calendarEvents.val()).format('YYYY-MM-DD')}?`,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((confirm) => {
                    if (confirm) {
                        $.post({
                            url: `{{ url('events') }}/${event.id}/schedule/toggle`,
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'PUT',
                                date: moment(calendarEvents.val()).format('YYYY-MM-DD'),
                                toggle: toggle ? 1 : 0,
                            }
                        })
                            .done(response => {
                                if (response.success) {
                                    timeSlotTable.ajax.reload()
                                    if (toggle)
                                        dateSchedule.push(moment(calendarEvents.val()).format('YYYY-MM-DD'))
                                    else
                                        removeItem(dateSchedule, moment(calendarEvents.val()).format('YYYY-MM-DD'))
                                    toggleDow()
                                    swal(`Tanggal berhasil di ${toggle ? 'Aktifkan' : 'Disable'}. ${response.message}`, {
                                        icon: "success",
                                    })
                                }
                            })
                            .fail(function (data) {
                                console.log('fail')
                                alert(data)
                                console.log(data)
                            })
                    }
                })
        }

        function deactivateDate() {
            swal({
                title: 'Konfirmasi',
                text: `Disable slot tgl ${moment(calendarEvents.val()).format('YYYY-MM-DD')}?`, icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((deactivate) => {
                    if (deactivate) {
                        $.post({
                            url: `{{ url('events') }}/${event.id}/schedule`,
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE',
                                date: moment(calendarEvents.val()).format('YYYY-MM-DD'),
                            }
                        })
                            .done(response => {
                                if (response.success) {
                                    timeSlotTable.ajax.reload()
                                    swal("Slot berhasil di Disable", {
                                        icon: "success",
                                    })
                                }
                            })
                            .fail(function (data) {
                                console.log('fail')
                                alert(data)
                                console.log(data)
                            })
                    }
                })
        }

        function removeItem(arr, value) {
            let i = 0
            while (i < arr.length) {
                if (arr[i] === value) {
                    arr.splice(i, 1)
                } else {
                    ++i
                }
            }
            return arr
        }

        $(function () {
            timeSlotTable = $('#timeSlotTable').DataTable({
                // data: timeSlotData,
                ajax: `{{ url('events') }}/${event.id || 999999}/slots?selectedDate={{ now()->max($event->date_start)->format('Y-m-d') }}`,
                dom: 'rti',
                columns: [
                    {data: "schedule_name"},
                    {
                        data: null,
                        name: 'start_time',
                        // render: data => {
                        //     return `${data.start_time} - ${data.end_time}`
                        // }
                        render: data => {
                            return `${data.start_time.length === 5 ? data.start_time : moment(data.start_time).format('HH:mm')} -
                            ${data.end_time.length === 5 ? data.end_time : moment(data.end_time).format('HH:mm')}`
                        }
                    },
                    {data: "slot"},
                    {
                        data: "is_active",
                        render: (data, type, full, meta) => {
                            return `<input type="checkbox" onclick="return false;" ${data ? 'CHECKED' : ''}>`
                        },
                        orderable: false,
                    },
                    {
                        data: null,
                        render: (data, type, full, meta) => {
                            return `<button class="btn btn-sm btn-primary" onclick="edit(${meta.row}, ${data.id})"><i class="cil-pencil"></i></button>`
                                + `<button class="btn btn-sm btn-danger" onclick="deleteRow(${meta.row}, ${data.id}, '${data.schedule_name}', '${data.start_time}', '${data.end_time}')"><i class="cil-trash"></i></button>`
                        },
                        orderable: false,
                    },
                ],
                order: [[1, 'asc']]
            })

            $('#dow :checkbox').on('change', function () {
                console.log($(this).data('dow'))
                $.post({
                    url: `{{ url('/events/') }}/${event.id}/dow/${$(this).data('dow')}`,
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'patch',
                        active: $(this).is(':checked') ? 1 : 0
                    }
                })
                toggleDow()
            })

            toggleDow()

            selectDate(calendarEvents.val())
        })
    </script>
@endsection
