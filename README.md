# NOAA Historical Climatology Dashboard

Laravel + ClickHouse + Vue.js

This project provides an interactive dashboard to visualize global climate data using the official **NOAA GHCN-Daily** dataset.

It uses **ClickHouse** as the OLAP engine, **Laravel** as the backend API, and **Vue.js 3** as the frontend dashboard.

# Overview

Architecture:

* **ClickHouse**
  * Stores NOAA staging, dimension, and fact tables
  * Uses **Materialized Views** to auto-build fact aggregates
* **Laravel API Backend**
  * `/countries` → distinct list of countries
  * `/weather` → aggregated daily weather metrics
  * `/date-range` → min/max available dates
* **Vue.js Frontend**
  * Country selector with search + select all
  * Date range picker
  * 8 climate charts (temperature, wind, precipitation, snow)
  * Dark mode
  * Responsive chart grid

# Requirement

Make sure you have:

* Docker & Docker Compose
* Git
* Node 18+
* Composer
* PowerShell/WSL (for import scripts)

# Setup and Run

```bash
git clone https://github.com/gfacheza/noaa_historical_climatology_dashboard.git
cd noaa_historical_climatology_dashboard
```

then you need to download the data from running file :

`./scripts/00_download_noaa.sh`

or you can just manually download it from :

`https://noaa-ghcn-pds.s3.amazonaws.com/csv.gz/${years}.csv.gz`

years is from 1900 .. 2022

you can add the years customly, such as 2018 to 2022

and you need to download the country database from:

`http://noaa-ghcn-pds.s3.amazonaws.com/ghcnd-countries.txt`

make sure your download file is in folder `./noaa`

Then all you need to do is to start the services:

```
docker compose up -d --build

```

This will start:

* **ClickHouse** (`8123`, `9000`)
* **Laravel API** (`8000`)
* **Frontend** (`5173`)
