<?php


namespace App\Http\Controllers;


use App\Models\Event;
use App\Models\EventSchedule;
use App\Models\EventType;
use App\Models\Location;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Storage;
use Throwable;

class VisitorController extends Controller
{
    private string $viewPrefix = 'dashboard.visitors.';

    public function index()
    {
        return view($this->viewPrefix . 'list');
    }

    public function fetch(Request $request)
    {
        $schedules = EventSchedule::with(['event'])->withCount('visits');
        if ($request->query('on_going')) {
            $schedules->whereDate('start_time', '=', today());
        }
        return DataTables::of($schedules)->make();
    }

    public function create(Request $request)
    {
        if ($request->has('schedule_id')) {
            return $this->addVisitorToSchedule($request);
        }
    }

    public function store(Request $request)
    {
        $schedule = EventSchedule::findOrFail($request->schedule_id);
        $availableSlot = $schedule->slot - $schedule->visits->count();
        if($request->groupMember > $availableSlot) {
            return redirect()->back()->withInput($request->input())
                ->withErrors("Melebihi sisa slot ({$availableSlot})");
        }

        try {
            DB::transaction(function () use ($request, $schedule) {
                $code = $this->createCode();
                $parent = Visitor::firstOrCreate(['email' => $request->email],
                    array_merge($request->input(), [
                        'password'=> $code,
                        'member' => '{}',
                    ]));
                $schedule->visits()->create([
                    'visitor_id' => $parent->id,
                    'code' => 'OFFLINE',
                    'member_count' => $request->groupMember + 1,
                    'confirmed_by' => auth()->id(),
                    'confirmed_at' => now(),
                    'read_at' => now(),
                ]);
            });
            return redirect('/schedules')->with(['status' => 'success', 'messages' => 'Offline visitor berhasil ditambahkan']);
        } catch (Throwable $e) {
            return redirect('/schedules')->withErrors($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $request->is_active = $request->is_active == 'on';
        $event->update($request->all(['event_name', 'event_type_id', 'open_booking_at',
            'date_start', 'date_end', 'location_id', 'notes', 'url', 'is_active']));
        return response()->json(['status' => 'success', 'event' => $event]);
    }

    public function slotStore(Request $request, $id)
    {
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

    public function edit($id)
    {
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

    public function delete($id)
    {
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

    public function addVisitorToSchedule(Request $request) {
        $schedule = EventSchedule::withCount(['visits' => function($query) {
            $query->select(DB::raw('SUM(member_count)'));
        }])->findOrFail($request->schedule_id);
        return view($this->viewPrefix . 'create')->with(['schedule' => $schedule]);
    }

    private function createRandomString($length)
    {
        $generated_string = "";
        $domain = '23456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($domain);
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, $len - 1);
            $generated_string = $generated_string . ($i == (int)($length / 2) ? '-' : '') . $domain[$index];
        }
        return $generated_string;
    }

    private function createCode()
    {
        $randomString = $this->createRandomString(8);
        $visit = Visit::where(['code' => $randomString])->with(['eventSchedule' => function ($query) {
            /** @var EventSchedule | Builder $query */
            $query->where('end_time', '>=', Carbon::tomorrow());
        }])->first();
        if ($visit) {
            return $this->createCode();
        }
        return $randomString;
    }
}
