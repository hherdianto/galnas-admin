<?php


namespace App\Http\Controllers;


use App\Models\AppConfig;
use App\Models\Visit;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index() {
        $config = AppConfig::find('confirmation.minutes.offset');
        return view('dashboard.scans.index')->with(['earliestLimit' => $config ? $config->value : 30]);
    }

    public function post(Request $request) {
        /** @var Visit $visit */
        $visit = Visit::where(['code' => $request->qrCodeMessage])->with(['visitor', 'eventSchedule', 'eventSchedule.event']
        )->orderBy('confirmed_at')->first();
        if ($visit) {
            $visitCount = Visit::where(['event_schedule_id' => $visit->event_schedule_id, 'code' => $request->qrCodeMessage])->count();
            return response()->json(['status' => 'success', 'visit' => $visit, 'visitCount' => $visitCount]);
        } else {
            return response()->json(['status' => 'fail', 'message' => "Code {$request->qrCodeMessage} tidak ditemukan"]);
        }
    }
}
