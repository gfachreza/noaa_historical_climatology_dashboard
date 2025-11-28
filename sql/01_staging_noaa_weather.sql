CREATE TABLE IF NOT EXISTS staging_noaa_weather
(
    station_id String,
    date Date,
    element String,
    value Float64,
    mflag String,
    qflag String,
    sflag String,
    time String
)
ENGINE = MergeTree()
ORDER BY (station_id, date, element);
