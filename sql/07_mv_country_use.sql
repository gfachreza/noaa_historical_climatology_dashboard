CREATE MATERIALIZED VIEW IF NOT EXISTS mv_country_use
TO dim_country_use
AS
SELECT
    country_code,
    any(country_name) AS country_name
FROM fact_noaa_country_daily
GROUP BY
    country_code;