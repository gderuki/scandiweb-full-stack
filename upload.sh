#!/bin/bash

if [ -f .env ]; then
    export $(cat .env | xargs)
else
    echo ".env file not found"
    exit 1
fi

REMOTE_USER="$REMOTE_USER"
REMOTE_HOST="$REMOTE_HOST"
REMOTE_PATH="$REMOTE_PATH"
IDENTITY_FILE="$IDENTITY_FILE"

read -p "Enter directories to upload (separated by space), or press enter to upload default directories: " -a USER_DIRECTORIES

DEFAULT_DIRECTORIES=("backend" "nginx" "database")

DIRECTORIES_TO_UPLOAD=()
if [ ${#USER_DIRECTORIES[@]} -eq 0 ]; then
    echo "No directories specified. Uploading default directories."
    DIRECTORIES_TO_UPLOAD=("${DEFAULT_DIRECTORIES[@]}")
else
    DIRECTORIES_TO_UPLOAD=("${USER_DIRECTORIES[@]}")
    echo "Uploading specified directories: ${DIRECTORIES_TO_UPLOAD[*]}"
fi

for dir in "${DIRECTORIES_TO_UPLOAD[@]}"; do
    echo "Uploading $dir..."
    scp -i "$IDENTITY_FILE" -r "$dir" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH"
done

echo "Uploading docker-compose.yml..."
scp -i "$IDENTITY_FILE" "docker-compose.yml" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH"

echo "All necessary files and directories uploaded successfully."