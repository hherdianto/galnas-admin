<?php


namespace App\Http\Controllers;


use App\Models\Event;
use App\Models\EventSchedule;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventScheduleController extends Controller {
    private $viewPrefix = 'dashboard.eventSchedules.';

    public function index() {
        return view($this->viewPrefix . 'list');
    }

    public function fetch(Request $request) {
        $schedules = EventSchedule::with(['event'])->withCount(['visits as visitor_count' => function ($query) {
            /** @var Builder $query */
            $query->select(DB::raw('SUM(member_count)'));
        }, 'confirmedVisits as confirmed_count' => function ($query) {
            /** @var Builder $query */
            $query->select(DB::raw('SUM(member_count)'));
        }]);
        if ($request->query('on_going')) {
            $schedules->whereDate('start_time', '=', today());
        }
        return DataTables::of($schedules)->make();
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function deactivate(Request $request, $id) {
        $event = Event::findOrFail($id);
        /** @var EventSchedule $schedules */
        $schedules = $event->schedules()->whereDate('start_time', '=', $request->date)->get();
        $visit = EventSchedule::join('visits', 'event_schedules.id', '=', 'visits.event_schedule_id')
            ->whereDate('start_time', '=', $request->date)->where(['event_id' => $id])->count();
        if ($visit > 0) {
            return response()->json(['success' => false, 'message' => "Terdapat $visit visit yg terdaftar pada tgl $request->date"]);
        } else {
            $event->schedules()->whereDate('start_time', '=', $request->date)
                ->update(['is_active' => 0, 'updated_by' => auth()->id()]);
            return response()->json(['success' => true]);
        }
    }

    public function toggleDate(Request $request, $id) {
        $event = Event::findOrFail($id);
        /** @var EventSchedule $schedules */
//        $schedules = $event->schedules()->whereDate('start_time', '=', $request->date)->get();
        $visit = EventSchedule::join('visits', 'event_schedules.id', '=', 'visits.event_schedule_id')
            ->whereDate('start_time', '=', $request->date)->where(['event_id' => $id])->count();
        $toggle = $request->get('toggle');
        $event->schedules()->whereDate('start_time', '=', $request->date)
            ->update(['is_active' => $toggle, 'updated_by' => auth()->id()]);
        return response()->json(['success' => true, 'message' => !$toggle && $visit > 0 ? "Terdapat $visit visit yg terdaftar pada tgl $request->date" : '']);
    }
}
