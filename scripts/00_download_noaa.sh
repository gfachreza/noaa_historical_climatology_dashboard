#!/bin/bash
set -e

NOAA_DIR="./noaa"
BASE_URL="https://noaa-ghcn-pds.s3.amazonaws.com"

YEARS=("2018" "2019" "2020" "2021" "2022")

echo "=========================================="
echo " NOAA Downloader (wget accelerated)"
echo "=========================================="

mkdir -p "$NOAA_DIR"

for YEAR in "${YEARS[@]}"; do
    FILE="$NOAA_DIR/${YEAR}.csv.gz"
    URL="$BASE_URL/csv.gz/${YEAR}.csv.gz"

    if [ -f "$FILE" ]; then
        echo "[SKIP] $FILE already exists"
    else
        echo "[WGET] $YEAR ..."
        wget -q --show-progress "$URL" -O "$FILE"
    fi
done

# Countries file
COUNTRY_FILE="$NOAA_DIR/ghcnd-countries.txt"
COUNTRY_URL="$BASE_URL/ghcnd-countries.txt"

if [ -f "$COUNTRY_FILE" ]; then
    echo "[SKIP] countries already exists"
else
    echo "[WGET] countries metadata ..."
    wget -q --show-progress "$COUNTRY_URL" -O "$COUNTRY_FILE"
fi

echo "=========================================="
echo " DONE! NOAA files downloaded successfully."
echo "=========================================="