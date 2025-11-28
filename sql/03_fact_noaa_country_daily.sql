CREATE TABLE IF NOT EXISTS fact_noaa_country_daily
(
    country_code String,
    country_name String,
    date Date,
    avg_temp Float64,
    min_temp Float64,
    max_temp Float64,
    avg_precip Float64,
    avg_snowfall Float64,
    avg_snowdepth Float64,
    avg_wind Float64,
    max_wind Float64,
    station_count UInt32
)
ENGINE = MergeTree()
ORDER BY (country_code, date);