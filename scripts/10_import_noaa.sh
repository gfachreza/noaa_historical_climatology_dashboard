#!/bin/bash
set -e

echo "Importing NOAA CSV.GZ..."

for f in /data/*.csv.gz; do
    echo "Loading $f ..."

    {
        echo "station_id,date,element,value,mflag,qflag,sflag,time"

        # normalize NOAA CSV to ALWAYS have 8 columns
        gzip -dc "$f" \
        | awk -F',' '{
            # Ensure exactly 8 fields
            for(i=1;i<=8;i++){
                if(i>NF) {
                    printf ",";
                } else {
                    printf "%s,", $i;
                }
            }
            printf "\n";
        }' \
        | sed 's/,$//'
    } \
    | clickhouse-client --query="INSERT INTO staging_noaa_weather FORMAT CSVWithNames"
done

echo 'NOAA import complete!'
