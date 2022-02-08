@extends('dashboard.base')

@section('css')
@endsection

@section('content')
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
                                            <label for="event_name">Nama</label>
                                            <input class="form-control" type="text" placeholder="{{ __('Nama') }}"
                                                   name="event_name" required autofocus id="event_name"
                                                   value="{{ $event->event_name }}">
                                        </div>

                                        <div class="form-group row">
                                            <label for="event_type_id">Tipe</label>
                                            <select class="form-control" name="event_type_id" id="event_type_id">
                                                @foreach($eventTypes as $eventType)
                                                    <option value="{{ $eventType->id }}"
                                                        {{ $event->event_type_id == $eventType->id ? 'SELECTED' : '' }}>
                                                        {{ $eventType->type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group row">
                                            <label for="location_id">Lokasi</label>
                                            <select class="form-control" name="location_id" id="location_id">
                                                @foreach($locations as /** @var \App\Models\Location $location */$location)
                                                    <option value="{{ $location->id }}"
                                                        {{ $event->location_id == $location->id }}>
                                                        {{ $location->location_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-12" for="event_date_start">Tgl Mulai</label>
                                            <div class="col-sm-5">
                                                <input type="date" class="form-control" name="date_start" required id="event_date_start"
                                                       value="{{ optional($event->date_start)->format('Y-m-d') }}"/>
                                            </div>
                                            <div class="col-sm-1">
                                                <label for="event_date_end">-</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="date" class="form-control" name="date_end" required id="event_date_end"
                                                       value="{{ optional($event->date_end)->format('Y-m-d') }}"/>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="event_open_booking_at">Open Booking</label>
                                            <input type="date" class="form-control" name="open_booking_at" required id="event_open_booking_at"
                                                   value="{{ optional($event->open_booking_at)->format('Y-m-d') ?: today()->format('Y-m-d') }}"/>
                                        </div>

                                        <div class="form-group row">
                                            <label for="event_url">URL</label>
                                            <input type="url" class="form-control" name="url" id="event_url"
                                                   value="{{ $event->url }}"/>
                                        </div>

                                        <div class="form-group row">
                                            <label for="textarea-input">Deskripsi</label>
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
                                    <button class="btn btn-block btn-success" onclick="post(true)"
                                            type="button">{{ __('Save & Return') }}</button>
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
                                                Termasuk akhir pekan <input type="checkbox" id="includeWeekend">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <button class="btn btn-primary m-2"
                                                    onclick="formAdd()">{{ __('Add Slot') }}</button>
                                        </div>
                                        <div class="col-sm-6">
                                            <button class="btn btn-primary m-2"
                                                    onclick="saveSlot()">{{ __('Simpan') }}</button>
                                        </div>
                                    </div>
                                    <br>
                                    <table class="table table-responsive-sm table-striped text-center"
                                           id="timeSlotTable">
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
                                    <div class="row">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <label class="control-label" for="schedule_name">Session Name</label>
                                    <input id="schedule_name" class="form-control" name="schedule_name"
                                           placeholder="Session #">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="start_time">Time</label>
                                    <input type="time" id="start_time" name="start_time" step="1800" value="09:00">
                                    <label class="control-label" for="end_time"> - </label>
                                    <input type="time" id="end_time" name="end_time" step="1800" value="10:00">
                                    <div class="help-block small">AM: setelah tengah malam, PM: setelah siang</div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="slot">Quota</label>
                                    <input type="number" name="slot" id="slot" min="0" value="20">
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
    <script type="text/javascript">
        let timeSlotTable;
        let timeSlotData =
                @if(!isset($timeSlots) || $timeSlots->count() == 0)
        [
            new TimeSlot('Sesi 1', '09:00', '10:00'),
            new TimeSlot('Sesi 2', '09:30', '10:30'),
            new TimeSlot('Sesi 3', '10:00', '11:00'),
            new TimeSlot('Sesi 4', '10:30', '11:30'),
            new TimeSlot('Sesi 5', '11:00', '12:00'),

            new TimeSlot('Sesi 6', '13:00', '14:00'),
            new TimeSlot('Sesi 7', '13:30', '14:30'),
            new TimeSlot('Sesi 8', '14:00', '15:00'),
        ];
            @else
            {!! $timeSlots->map(function ($item) {
        /** @var \App\Models\EventSchedule $item */
        return [
            'schedule_name' => $item->schedule_name,
            'start_time' => $item->start_time->format('H:i'),
            'end_time' => $item->end_time->format('H:i'),
            'slot' => $item->slot,
            'is_active' => $item->is_active,
            ];
    })->toJson() !!}
            @endif
        let selectedIndex = -1;
        let modal = $('#editSlotTimeDialog');
        let event = @if($event->id)
            {!!  $event->toJson() !!}
            @else
        {}
            @endif

        let editTimeSlotModal = new coreui.Modal(document.getElementById('editSlotTimeDialog'), {
                keyboard: false
            })

        function TimeSlot(schedule_name, start_time, end_time, slot = 20, is_active = 1) {
            this.schedule_name = schedule_name;
            this.start_time = start_time;
            this.end_time = end_time;
            this.slot = slot;
            this.is_active = is_active;
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
                            .text(`${moment(response.event.date_start).format('Y-MM-DD')}
                            - ${moment(response.event.date_end).format('Y-MM-DD')}`)
                    }
                })
                .fail(function (data) {
                    alert(data)
                })
        }

        function saveSlot() {
            $.post({
                url: `{{ url('/events/') }}/${event.id}/slots`,
                data: {
                    _token: '{{ csrf_token() }}',
                    slots: timeSlotTable.data().toArray(),
                    include_week_end: $('#includeWeekend').is(':checked')
                }
            })
                .done(function (response) {
                    window.location = '{{ url('events') }}'
                })
                .fail(function (data) {
                    console.log('fail')
                    alert(data)
                    console.log(data)
                })
        }

        function edit(row) {
            editTimeSlotModal.show()
            selectedIndex = row
            let timeSlot = timeSlotTable.row(row).data()
            modal.find('[name=schedule_name]').val(timeSlot.schedule_name)
            modal.find('[name=start_time]').val(timeSlot.start_time)
            modal.find('[name=end_time]').val(timeSlot.end_time)
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
        }

        function deleteRow(row) {
            timeSlotTable.row(row).remove()
            timeSlotTable.draw(false)
        }

        function saveTemplate() {
            let data = new TimeSlot()
            modal.find('form').serializeArray().map(item => {
                data[item.name] = item.value
            })
            if (selectedIndex > -1)
                timeSlotTable.row(selectedIndex).data(data)
            else timeSlotTable.row.add(data).draw(false)
        }

        $(function () {
            timeSlotTable = $('#timeSlotTable').DataTable({
                data: timeSlotData,
                dom: 'rti',
                columns: [
                    {data: "schedule_name"},
                    {
                        data: null,
                        render: data => {
                            return `${data.start_time} - ${data.end_time}`
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
                            return `<button class="btn btn-sm btn-primary" onclick="edit(${meta.row})"><i class="cil-pencil"></i></button>`
                                + `<button class="btn btn-sm btn-danger" onclick="deleteRow(${meta.row})"><i class="cil-trash"></i></button>`
                        },
                        orderable: false,
                    },
                ]
            })
        })
    </script>
@endsection
