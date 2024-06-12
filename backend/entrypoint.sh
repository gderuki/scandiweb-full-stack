#!/bin/bash

# setup schemas & run migrations
/app/migrate.sh

# web server
exec php -S 0.0.0.0:8000 -t /app/public