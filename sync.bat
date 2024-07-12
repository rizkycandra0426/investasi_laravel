@echo off

:: Local Database Export
set mysqldump="C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" 
set local_host="localhost"
set local_user="root"
set local_password=""
set local_database="investasi_db"
set local_backup="local.backup.sql"

%mysqldump% -h %local_host% -u %local_user% -p%local_password% %local_database% > %local_backup%
echo Local database exported to %local_backup%

:: Remote Database Export (Password in Plain Text - Less Secure)
set remote_host="103.175.220.92"
set remote_user="investasi_rizky_db"
set remote_password="iyiGspLZ5M3mNJdw"
set remote_database="investasi_rizky_db"
set remote_backup="remote.backup.sql"

%mysqldump% -h %remote_host% -u %remote_user% -p%remote_password% %remote_database% > %remote_backup%
echo Remote database exported to %remote_backup%
