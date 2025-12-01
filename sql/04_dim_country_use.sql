CREATE TABLE IF NOT EXISTS dim_country_use
(
    country_code String,
    country_name String
)
ENGINE = ReplacingMergeTree()
ORDER BY country_code;
