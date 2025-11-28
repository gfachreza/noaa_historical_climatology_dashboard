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
       1. META — ALL COUNTRIES
    =========================== */
    public function countries()
    {
        $sql = "
            SELECT DISTINCT country_code, country_name
            FROM fact_noaa_country_daily
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
                min(date) AS min_date,
                max(date) AS max_date
            FROM fact_noaa_country_daily
            FORMAT JSON
        ";

        $res = $this->ch->query($sql);

        return [
            "min" => $res[0]["min_date"],
            "max" => $res[0]["max_date"]
        ];
    }

    /* ===========================
       3. MAIN WEATHER ENDPOINT
    =========================== */
    public function index(Request $request)
    {
        // PARAMETER FIX — gunakan start_date/end_date
        $start = $request->query('start_date') ?? $request->query('start');
        $end   = $request->query('end_date')   ?? $request->query('end');

        // VALIDASI SIMPLE
        if (!$start || !$end) {
            return response()->json([
                "error" => "start_date and end_date are required"
            ], 400);
        }

        // FIX param countries
        $countries = $request->query('countries', []);

        if (!is_array($countries)) {
            $countries = explode(",", $countries);
        }

        $countries = array_filter($countries); // remove empty

        // Jika tidak ada country → ambil semua
        if (empty($countries)) {
            $countries = array_column(
                $this->ch->query("SELECT DISTINCT country_code FROM fact_noaa_country_daily"),
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
       4. OPTIONAL COMPARE
    =========================== */
    public function compare(Request $request)
    {
        return ["message" => "compare endpoint is available"];
    }
}
