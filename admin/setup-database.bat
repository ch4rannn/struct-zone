@echo off
echo Creating structzone_db database...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS structzone_db;"
echo Importing schema...
"C:\xampp\mysql\bin\mysql.exe" -u root structzone_db < "E:\website creation\structzone\admin\schema.sql"
echo Done!
pause
