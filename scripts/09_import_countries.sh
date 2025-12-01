#!/bin/bash
set -e

echo "Importing countries..."

awk '
{
    code = substr($0, 1, 2);          # 2 huruf negara
    name = substr($0, 4);             # mulai kolom ke-4
    gsub(/^ +| +$/, "", name);        # trim whitespace
    printf "\"%s\",\"%s\"\n", code, name;
}
' /data/ghcnd-countries.txt \
| clickhouse-client --query="INSERT INTO dim_countries FORMAT CSV"

echo "Countries imported successfully!"
