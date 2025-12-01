CREATE MATERIALIZED VIEW IF NOT EXISTS mv_date_range
TO dim_date_range
AS
SELECT
    minState(date) AS min_date_state,
    maxState(date) AS max_date_state
FROM fact_noaa_country_daily;
