@REM kill all process that use port 8000
fuser -k 8000/tcp && php artisan serve --host 0.0.0.0&