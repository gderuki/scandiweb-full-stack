Uncomment db migration script if running on fresh environment:

Command could be found in `entrypoint.sh`:

>\# setup schemas & run migrations <br/>
>\# /app/migrate.sh

Once local environment is setup at least once, these lines could be commented out. It changes nothing, just makes system init less verbose.