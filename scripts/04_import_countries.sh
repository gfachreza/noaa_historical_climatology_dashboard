#!/bin/bash
set -e

echo "Importing countries..."

awk '
{
    code = $1;
    name = substr($0, index($0,$2));
    gsub(/"/, "\"\"");
    print "\"" code "\",\"" name "\"";
}
' /data/ghcnd-countries.txt \
| clickhouse-client --query="INSERT INTO dim_countries FORMAT CSV"

echo "Countries imported successfully!"
