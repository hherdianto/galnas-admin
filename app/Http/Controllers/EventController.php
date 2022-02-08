<?php


namespace App\Http\Controllers;

use App\Models\DayOfWeekEvent;
use App\Models\Event;
use App\Models\EventSchedule;
use App\Models\EventType;
use App\Models\Location;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Storage;
use Throwable;

class EventController extends Controller {
    private string $viewPrefix = 'dashboard.events.';

    public function index() {
        return view($this->viewPrefix . 'list');
    }

    public function fetch() {
        $events = Event::with(['eventType', 'location'])->orderBy('date_start', 'desc');
        return DataTables::of($events)->make();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function getTimeSlots(Request $request, $id) {
        $event = Event::find($id);
        $timeSlots = EventSchedule::whereDate('start_time', '=', $request->selectedDate)
            ->where(['event_id' => $id])->orderBy('start_time');
        if ($event)
            for ($i = 0; $i < 7; $i++) {
                $event->dayOfWeeks()->firstOrCreate([
                    'day_of_week' => $i
                ], ['active' => 1]);
            }
        return DataTables::of($timeSlots)->make();
    }

    public function create() {
        $eventTypes = EventType::whereIsShowing(1)->get();
        $locations = Location::whereIsShowing(1)->get();
        $event = new Event();
        $event->is_active = 1;
        $dows = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
        ];
        EventSchedule::orWhereDate('start_time', '=', '2021-05-05')->get();
        return view($this->viewPrefix . 'create')->with([
            'eventTypes' => $eventTypes,
            'locations' => $locations,
            'event' => $event,
            'dows' => $dows
        ]);
    }

    public function store(Request $request) {
        $input = $request->input();
        $input['is_active'] = $request->get('is_active', 0);
        $event = Event::create($input);
        return response()->json(['status' => 'success', 'event' => $event]);
    }

    public function update(Request $request, $id) {
        $event = Event::findOrFail($id);
        $input = $request->all(['event_name', 'event_type_id', 'open_booking_at',
            'date_start', 'date_end', 'location_id', 'notes', 'url', 'is_active']);
        $input['is_active'] = $request->get('is_active', 0);
        $oldEndDate = $event->date_end;
        $event->update($input);

        $day = $oldEndDate->max(Carbon::yesterday())->addDay();
        $schedulesToSave = [];
        $scheduleTemplates = $this->dayOfWeekTemplates($event);
        while ($day->lte($event->date_end)) {
            if ($event->schedules()->whereDate('start_time', '=', $day)->get()->isEmpty()) {
                $schedulesToSave = array_merge($schedulesToSave, $this->schedulesToInsert($scheduleTemplates[$day->dayOfWeek], $day, $event));
                $day = $day->addDay();
            }
        }
        if (sizeof($schedulesToSave) > 0) {
            $event->schedules()->saveMany($schedulesToSave);
        }
        return response()->json(['status' => 'success', 'event' => $event]);
    }

    /**
     * Update Day of Week event
     * @param Request $request
     * @param $id
     * @param $dayOfWeek
     * @return DayOfWeekEvent|Model
     */
    public function updateDow(Request $request, $id, $dayOfWeek) {
        return DayOfWeekEvent::updateOrCreate(['event_id' => $id, 'day_of_week' => $dayOfWeek], ['active' => $request->active]);
    }

    public function slotStore(Request $request, $id) {
        $event = Event::findOrFail($id);
        $date = Carbon::make($request->date);
        $schedules = $event->schedules()->whereDate('start_time', '=', $request->date)->get();
        try {
            DB::transaction(function () use ($request, $event, $schedules, $date) {
                $day = $event->date_start;
                $schedulesToSave = [];
                $dateToRemove = [];
                $removeOldSchedules = false;
                while ($day->lte($event->date_end)) {
                    if ($day == $date) {
                        $day = $day->addDay();
                        continue;
                    }
                    switch ($request->repeat) {
                        case 'dow':
                            if ($day->dayOfWeek == $date->dayOfWeek) {
//                                $schedulesToRemove->orWhereDate('start_time', '=', $day);
                                $dateToRemove[] = $day->format('Y-m-d');
                                $schedulesToSave = array_merge($schedulesToSave, $this->schedulesToInsert($schedules, $day, $event));
                            }
                            break;
                        case 'all':
                            if ($event->dayOfWeeks->where('day_of_week', '=', $day->dayOfWeek)->first->active) {
                                $removeOldSchedules = true;
                                $schedulesToSave = array_merge($schedulesToSave, $this->schedulesToInsert($schedules, $day, $event));
                            }
                            break;
                        default:
                            break;
                    }
                    $day = $day->addDay();
                }
                logger($dateToRemove);
                if ($removeOldSchedules) {
                    $event->schedules()->whereDate('start_time', '!=', $date)->delete();
                } else if (sizeof($dateToRemove) > 0) {
                    $event->schedules()->whereIN(DB::raw('DATE(start_time)'), $dateToRemove)->delete();
                }
                if (sizeof($schedulesToSave) > 0) {
                    $event->schedules()->saveMany($schedulesToSave);
                }
            });
            return response()->json(['status' => 'success', 'schedulesCount' => $event->schedules->count()]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            return response()->json(['status' => 'error', 'messages' => $e->getMessage()], 500);
        }
    }

    /**
     * Save individual schedule
     * @param Request $request
     * @param $id
     * @return EventSchedule|Model
     */
    public function scheduleStore(Request $request, $id) {
        $start_time = $request->date . ' ' . $request->start_time;
        $end_time = $request->date . ' ' . $request->end_time;
        return EventSchedule::updateOrCreate(
            ['id' => $request->id],
            [
                'event_id' => $id,
                'schedule_name' => $request->schedule_name,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'slot' => $request->slot,
                'is_active' => $request->is_active,
                'created_by' => auth()->id(),
            ]);
    }

    public function edit($id) {
        $event = Event::findOrFail($id);
        $eventTypes = EventType::whereIsShowing(1)->get();
        $locations = Location::whereIsShowing(1)->get();
        $firstDate = $event->schedules()->orderBy('start_time')->first();
        $timeSlots = collect([]);
        /** @var EventSchedule $firstDate */
        if ($firstDate) {
            $timeSlots = $event->schedules()
                ->whereDate('start_time', '=', $firstDate->start_time->format('Y-m-d'))->get()
                ->map(function ($schedule) {
                    /** @var EventSchedule $schedule */
                    $schedule->start_time = $schedule->start_time->format('H:i');
                    return $schedule;
                });
        }
        return view($this->viewPrefix . 'create')->with([
            'eventTypes' => $eventTypes,
            'locations' => $locations,
            'event' => $event,
            'timeSlots' => $timeSlots,
        ]);
    }

    public function delete($id) {
        try {
            Event::findOrFail($id)->delete();
            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return response()->json(['status' => 'failed', 'messages' => $e->getMessage()]);
        }
    }

    public function addDate(Request $request, $id) {
        $event = Event::findOrFail($id);
        if ($event->date_end == $request->end_date) {
            return response()->json(['status' => 'error', 'messages' => 'Tanggal akhir tidak berubah'], 500);
        }
        $day = $event->date_end->addDay();
        $event->date_end = $request->end_date;
        $schedulesToSave = [];
        while ($day->lte($event->date_end)) {
            $dow = $day->dayOfWeek + 1;
            /** @var string $templateDate */
            $templateDate = $event->schedules()->whereRaw("DAYOFWEEK(start_time) = $dow")
                ->max(DB::raw('DATE(start_time)'));
            if ($templateDate) {
                $templateSchedules = $event->schedules()->whereDate('start_time', '=', $templateDate)
                    ->get();
                $schedulesToSave = array_merge($schedulesToSave, $this->schedulesToInsert($templateSchedules, $day, $event));
            }
            $day = $day->addDay();
        }
        DB::transaction(function () use ($schedulesToSave, $event) {
            if (sizeof($schedulesToSave) > 0) {
                $event->schedules()->saveMany($schedulesToSave);
            }
            $event->save();
        });
        return response()->json(['success' => true]);
    }

    public function saveImage(Request $request, $id) {
        $event = Event::findOrFail($id);
        $image = $request->file('image');
        $path = Storage::disk('public_uploads')->put('images', $image);
//        $path = $image->storePublicly('images');
        $event->images()->create(['image' => $path, 'created_by' => auth()->user()->id]);
    }

    /**
     * @param $templateSchedules
     * @param $day
     * @param $event
     * @return array
     */
    private function schedulesToInsert($templateSchedules, Carbon $day, $event): array {
        $newSchedules = [];
        if ($templateSchedules)
            foreach ($templateSchedules as $templateSchedule) {
                /** @var EventSchedule $templateSchedule */
                $newSchedule = new EventSchedule();
                $newSchedule->schedule_name = $templateSchedule->schedule_name;
                $newSchedule->start_time = $templateSchedule->start_time->setDate($day->year, $day->month, $day->day);
                $newSchedule->end_time = $templateSchedule->end_time->setDate($day->year, $day->month, $day->day);
                $newSchedule->slot = $templateSchedule->slot;
                $newSchedule->created_by = auth()->id();
                $newSchedules[] = $newSchedule;
            }
        return $newSchedules;
    }

    /**
     * @param Event $event
     * @return EventSchedule[]
     */
    private function dayOfWeekTemplates($event) {
        $dateCount = $event->schedules()->selectRaw('DATE(start_time) AS dates')->groupByRaw('dates')->get()->count();
        if ($dateCount >= 7)
            $dates = EventSchedule::whereDate('start_time', '<=', now()->subWeek()->max($event->date_start->addWeek()->subDay()))
                ->where(['event_id' => $event->id])->orderBy('dates', 'desc')
                ->selectRaw('DATE(start_time) AS dates')->groupByRaw('dates')->limit(7)->get();
        else $dates = $event->schedules()->selectRaw('DATE(start_time) AS dates')->groupByRaw('dates')->get();

        /** @var EventSchedule[] $schedules */
        $schedules = [];
        $dates->each(function ($date, $key) use ($event, &$schedules) {
            /** @var Carbon $date */
            logger(Carbon::parse($date->dates)->dayOfWeek);
//            logger($schedules[$date->dates->dayOfWeek]);
            $schedules[Carbon::parse($date->dates)->dayOfWeek] = $event->schedules()->whereDate('start_time', '=', $date->dates)
                ->orderBy('start_time')->get();
        });
        return $schedules;
    }
}
