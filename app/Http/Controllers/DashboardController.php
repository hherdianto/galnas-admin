<?php


namespace App\Http\Controllers;


use App\Models\Visit;
use DataTables;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $online = Visit::where('code', '!=', 'OFFLINE')->sum('member_count');
        $offline = Visit::where('code', '=', 'OFFLINE')->sum('member_count');
        $confirmed = Visit::whereNotNull('confirmed_at')->sum('member_count');
        $uniqueVisitor = DB::table('visitors')->count(DB::raw('DISTINCT email'));
        return view('dashboard.homepage')->with([
            'online' => $online,
            'offline' => $offline,
            'confirmed' => $confirmed,
            'uniqueVisitor' => $uniqueVisitor,
        ]);
    }

    public function onlinePerDay()
    {
        $visitPerDay = DB::select(DB::raw('SELECT
            DATE(es.start_time) AS evDate,
            SUM(vs.member_count) AS total
        FROM
            event_schedules AS es
            INNER JOIN
            visits AS vs
            ON
		es.id = vs.event_schedule_id
		WHERE DATE(es.start_time) >= DATE(NOW()) AND vs.code != "OFFLINE"
		GROUP BY evDate
		LIMIT 0, 7'));
        return response()->json($visitPerDay);
    }

    public function offlinePerDay()
    {
        $visitPerDay = DB::select(DB::raw('SELECT * FROM (
        SELECT
            DATE(es.start_time) AS evDate,
            SUM(vs.member_count) AS total
        FROM
            event_schedules AS es
            INNER JOIN
            visits AS vs
            ON
		es.id = vs.event_schedule_id
		WHERE DATE(es.start_time) <= DATE(NOW()) AND vs.code = "OFFLINE"
		GROUP BY evDate
        ORDER BY evDate DESC
		LIMIT 0, 7
    ) sub
            ORDER BY evDate
    '));
        return response()->json($visitPerDay);
    }

    public function confirmedPerDay()
    {
        $visitPerDay = DB::select(DB::raw('SELECT * FROM (
            SELECT
                DATE(es.start_time) AS evDate,
                SUM(vs.member_count) AS total
            FROM
                event_schedules AS es
                INNER JOIN
                visits AS vs
                ON
            es.id = vs.event_schedule_id
            WHERE DATE(es.start_time) <= DATE(NOW()) AND vs.confirmed_at IS NOT NULL
            GROUP BY evDate
            ORDER BY evDate DESC
            LIMIT 0, 7
        ) sub
            ORDER BY evDate
'));
        return response()->json($visitPerDay);
    }

    public function registerPerDay()
    {
        $visitPerDay = DB::select(DB::raw('SELECT * FROM (
            SELECT
                COUNT(DISTINCT vt.email) AS total,
                DATE(vt.created_at) AS regDate
            FROM
                visitors AS vt
                GROUP BY regDate
                ORDER BY regDate DESC
                LIMIT 0, 7
            ) sub
            ORDER BY regDate '));
        return response()->json($visitPerDay);
    }

    public function visitPerEvent()
    {
        $events = DB::select(DB::raw('SELECT
            ev.event_name,
            MIN(ev.date_start) AS date_start,
            MAX(ev.date_end) AS date_end,
            SUM(IF(vs.code = "OFFLINE", 0, vs.member_count)) as totalOnline,
            SUM(IF(vs.code = "OFFLINE", vs.member_count, 0)) as totalOffline,
            SUM(IF(vs.code != "OFFLINE" AND vs.confirmed_at IS NOT NULL , vs.member_count, 0)) as totalOnlineConfirmed,
            SUM(IF(vs.confirmed_at IS NOT NULL , vs.member_count, 0)) as totalConfirmed
        FROM
            `events` AS ev
            INNER JOIN
            event_schedules AS es
            ON
                ev.id = es.event_id
            INNER JOIN
            visits AS vs
            ON
                es.id = vs.event_schedule_id
        GROUP BY
	ev.id, ev.event_name'));
        return DataTables::of($events)->make();
    }
}
