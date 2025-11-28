FROM clickhouse/clickhouse-server:latest

COPY scripts/04_import_countries.sh /docker-entrypoint-initdb.d/
COPY scripts/05_import_noaa.sh /docker-entrypoint-initdb.d/
COPY scripts/06_fact_join.sh /docker-entrypoint-initdb.d/
COPY sql/* /docker-entrypoint-initdb.d/

RUN chmod +x /docker-entrypoint-initdb.d/*.sh