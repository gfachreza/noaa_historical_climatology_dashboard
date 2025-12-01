CREATE TABLE IF NOT EXISTS dim_date_range
(
    min_date_state AggregateFunction(min, Date),
    max_date_state AggregateFunction(max, Date)
)
ENGINE = AggregatingMergeTree()
ORDER BY tuple();