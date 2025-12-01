<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClickhouseService;

class WeatherController extends Controller
{
    protected ClickhouseService $ch;

    public function __construct(ClickhouseService $ch)
    {
        $this->ch = $ch;
    }

    /* ===========================
       1. META — ALL COUNTRIES (FAST)
    =========================== */
    public function countries()
    {
        $sql = "
            SELECT country_code, country_name
            FROM dim_country_use
            ORDER BY country_name ASC
            FORMAT JSON
        ";

        return $this->ch->query($sql);
    }

    /* ===========================
       2. META — MIN/MAX DATE
    =========================== */
    public function dateRange()
    {
        $sql = "
            SELECT 
                minMerge(min_date_state) AS min_date,
                maxMerge(max_date_state) AS max_date
            FROM dim_date_range
            FORMAT JSON
        ";

        $res = $this->ch->query($sql);

        return [
            "min" => $res[0]["min_date"],
            "max" => $res[0]["max_date"]
        ];
    }

    /* ===========================
       3. MAIN WEATHER QUERY
    =========================== */
    public function index(Request $request)
    {
        // FIX params
        $start = $request->query('start_date') ?? $request->query('start');
        $end   = $request->query('end_date')   ?? $request->query('end');

        if (!$start || !$end) {
            return response()->json([
                "error" => "start_date and end_date are required"
            ], 400);
        }

        // Countries param
        $countries = $request->query('countries', []);

        if (!is_array($countries)) {
            $countries = explode(",", $countries);
        }

        $countries = array_filter($countries);

        // If empty → get from dim_country_use (FAST)
        if (empty($countries)) {
            $countries = array_column(
                $this->ch->query("
                    SELECT country_code FROM dim_country_use
                "),
                "country_code"
            );
        }

        $list = "'" . implode("','", $countries) . "'";

        $sql = "
            SELECT *
            FROM fact_noaa_country_daily
            WHERE date BETWEEN '$start' AND '$end'
            AND country_code IN ($list)
            ORDER BY date ASC
            FORMAT JSON
        ";

        return $this->ch->query($sql);
    }

    /* ===========================
       4. COMPARE ENDPOINT
    =========================== */
    public function compare(Request $request)
    {
        return ["message" => "compare endpoint is available"];
    }
}