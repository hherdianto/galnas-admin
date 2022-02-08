<?php


namespace App\Http\Controllers;


use App\Models\Event;
use App\Models\EventSchedule;
use App\Models\EventType;
use App\Models\Location;
use App\Models\Visit;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Storage;
use Throwable;

class EventVisitController extends Controller {
    private string $viewPrefix = 'dashboard.visits.';

    public function index() {
        $from = EventSchedule::withTrashed()->min('start_time');
        $to = EventSchedule::withTrashed()->max('start_time');
        logger($to);
        logger($from);
        return view($this->viewPrefix . 'list', ['from' => $from, 'to' => $to]);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function fetch(Request $request) {
        /** @var Visit $visits */
        $visits = Visit::with(['eventSchedule' => function ($query) {
            /** @var EventSchedule | Builder $query */
            $query->withTrashed();
        }, 'visitor:id,full_name,email,phone,gender,age,indonesian', 'eventSchedule.event' => function ($query) {
            /** @var Event | Builder $query */
            $query->withTrashed();
        }]);
        if ($request->query('schedule_id')) {
            $visits->where(['event_schedule_id' => $request->query('schedule_id')]);
        }
        if ($request->query('online_only')) {
            $visits->where('code', '!=', 'OFFLINE');
        } elseif ($request->query('offline_only')) {
            $visits->where('code', '=', 'OFFLINE');
        }
//        if ($request->query('start_time_gte_')) {
        $date = $request->query('start_time_gte_');
        $visits->whereHas('eventSchedule', function ($query) use ($date) {
            try {
                $date = Carbon::parse($date);
            } catch (InvalidFormatException $ex) {
                $date = today();
            }
            $query->whereDate('start_time', '>=', $date);
        });
//        }
        if ($filterType = $request->query('filterType')) {
            $filterVal = $request->query('filterVal');
            logger($filterVal);
            switch ($filterType) {
                case 'dates':
                    foreach ($filterVal as $idx => $date) {
                        if (strlen($date) > 0)
                            try {
                                $date = Carbon::parse($date);
                                $visits->whereHas('eventSchedule', function ($query) use ($date, $idx) {
                                    $query->whereDate('start_time', !$idx ? '>=' : '<=', $date);
                                });
                            } catch (InvalidFormatException $ex) {
                            }
                    }
                    break;
                case 'months':
                    $visits->whereHas('eventSchedule', function ($query) use ($filterVal) {
                        if ($filterVal)
                            $query->withTrashed()->where('start_time', 'LIKE', "$filterVal%");
                    });
                    break;
                default:
                    break;
            }
        }
        logger($visits->toSql());
        $visits = $visits->select(['visits.id', 'visitor_id', 'event_schedule_id', 'member_count', 'code', 'confirmed_at']);
        return DataTables::of($visits)->make();
    }

    public function get(Request $request, $id) {
        /** @var Visit $visit */
        $visit = Visit::where(['id' => $id])->with(['visitor', 'eventSchedule', 'eventSchedule.event']
        )->orderBy('confirmed_at')->first();
        if ($visit) {
            $visitCount = Visit::where(['event_schedule_id' => $visit->event_schedule_id, 'code' => $request->qrCodeMessage])->count();
            return response()->json(['status' => 'success', 'visit' => $visit, 'visitCount' => $visitCount]);
        } else {
            return response()->json(['status' => 'fail', 'message' => "Code {$request->qrCodeMessage} tidak ditemukan"]);
        }
    }

    public function create() {
        $eventTypes = EventType::whereIsShowing(1)->get();
        $locations = Location::whereIsShowing(1)->get();
        $event = new Event();
        $event->is_active = 1;
        return view($this->viewPrefix . 'create')->with([
            'eventTypes' => $eventTypes,
            'locations' => $locations,
            'event' => $event,
        ]);
    }

    public function store(Request $request) {
        $event = Event::create($request->input());
        return response()->json(['status' => 'success', 'event' => $event]);
    }

    public function update(Request $request, $id) {
        $event = Event::findOrFail($id);
        $request->is_active = $request->is_active == 'on';
        $event->update($request->all(['event_name', 'event_type_id', 'open_booking_at',
            'date_start', 'date_end', 'location_id', 'notes', 'url', 'is_active']));
        return response()->json(['status' => 'success', 'event' => $event]);
    }

    public function slotStore(Request $request, $id) {
        $event = Event::findOrFail($id);
        try {
            DB::transaction(function () use ($request, $event) {
                $event->schedules()->delete();
                $day = $event->date_start;
                while ($day->lte($event->date_end)) {
                    if ($day->isWeekend() && $request->include_week_end)
                        foreach ($request->slots as $slot) {
                            $schedule = new EventSchedule($slot);
                            $schedule->start_time = $schedule->start_time->setDate($day->year, $day->month, $day->day);
                            $schedule->end_time = $schedule->end_time->setDate($day->year, $day->month, $day->day);
                            $schedule->created_by = auth()->user()->id;
                            $event->schedules()->save($schedule);
                        }
                    $day->addDay();
                }
            });
            return response()->json(['status' => 'success', 'schedulesCount' => $event->schedules->count()]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            return response()->json(['status' => 'error', 'messages' => $e->getMessage()], 500);
        }
    }

    public function confirm($id) {
        $visit = Visit::findOrFail($id);
        $group = $visit->eventSchedule->visits()->where(['code' => $visit->code])
            ->update(['confirmed_by' => auth()->id(), 'confirmed_at' => now()]);
        return response()->json(['status' => 'success', 'visit' => $visit]);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function remove($id) {
        $visit = Visit::findOrFail($id);
        $visit->delete();
        return response()->json(['status' => 'success']);
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

    public function saveImage(Request $request, $id) {
        $event = Event::findOrFail($id);
        $image = $request->file('image');
        $path = Storage::disk('public_uploads')->put('images', $image);
//        $path = $image->storePublicly('images');
        $event->images()->create(['image' => $path, 'created_by' => auth()->user()->id]);
    }
}
