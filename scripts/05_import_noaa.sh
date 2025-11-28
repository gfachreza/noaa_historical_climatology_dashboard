#!/bin/bash
set -e

echo "Importing NOAA CSV.GZ..."

for f in /data/*.csv.gz; do
    echo "Loading $f ..."
    gzip -dc "$f" \
        | clickhouse-client --query="INSERT INTO staging_noaa_weather FORMAT CSV"
done

echo "NOAA import complete!"
