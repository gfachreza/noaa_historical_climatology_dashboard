#!/bin/bash
set -e

echo "Building fact_noaa_country_daily ..."

clickhouse-client --query="
INSERT INTO fact_noaa_country_daily
SELECT
    c.country_code,
    c.country_name,
    w.date,

    avgIf(w.value, w.element = 'TAVG') AS avg_temp,
    minIf(w.value, w.element = 'TMIN') AS min_temp,
    maxIf(w.value, w.element = 'TMAX') AS max_temp,

    avgIf(w.value, w.element = 'PRCP') AS avg_precip,
    avgIf(w.value, w.element = 'SNOW') AS avg_snowfall,
    avgIf(w.value, w.element = 'SNWD') AS avg_snowdepth,

    avgIf(w.value, w.element = 'AWND') AS avg_wind,
    maxIf(w.value, w.element IN ('WSF2','WSF5')) AS max_wind,

    uniqExact(w.station_id) AS station_count
FROM staging_noaa_weather w
LEFT JOIN dim_countries c 
    ON c.country_code = substring(w.station_id, 1, 2)
GROUP BY
    c.country_code,
    c.country_name,
    w.date
ORDER BY
    c.country_code,
    w.date;
"

echo "Fact table build complete!"