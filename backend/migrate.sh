#!/bin/bash

log() {
    local level=$1
    local message=$2
    echo "$(date '+%Y-%m-%d %H:%M:%S') [$level] $message"
}

log "INFO" "Creating schemas and populating db with vendor data..."

log "INFO" "Checking MySQL readiness..."
until nc -z ${DB_HOST} ${DB_PORT}; do
    log "WARN" "MySQL is NOT UP yet, waiting..."
    sleep 2
done
log "INFO" "MySQL is UP AND READY for connections."

log "INFO" "Running CreateSchemas.php..."
if php src/Utils/CreateSchemas.php; then
    log "INFO" "CreateSchemas.php SUCCEEDED."

    log "INFO" "Running PopulateData.php..."
    if php src/Utils/PopulateData.php; then
        log "INFO" "PopulateData.php SUCCEEDED."
    else
        log "ERROR" "PopulateData.php FAILED."
        exit 1
    fi
else
    log "ERROR" "CreateSchemas.php FAILED."
    exit 1
fi