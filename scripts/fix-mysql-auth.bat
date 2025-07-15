@echo off
REM AHZ Stack PHP - MySQL Authentication Fix Script
REM This script helps fix MySQL authentication plugin issues

echo ========================================
echo AHZ Stack PHP - MySQL Authentication Fix
echo ========================================
echo.

echo Checking Docker containers status...
docker-compose ps
echo.

echo Options:
echo 1. Restart MySQL with authentication fix (preserves data)
echo 2. Reset MySQL completely (WARNING: deletes all data)
echo 3. Check current authentication plugins
echo 4. Test database connection
echo 5. View MySQL logs
echo 6. Exit
echo.

set /p choice=Enter your choice (1-6): 

if "%choice%"=="1" goto restart_mysql
if "%choice%"=="2" goto reset_mysql
if "%choice%"=="3" goto check_auth
if "%choice%"=="4" goto test_connection
if "%choice%"=="5" goto view_logs
if "%choice%"=="6" goto exit

echo Invalid choice. Please try again.
pause
goto start

:restart_mysql
echo.
echo Restarting MySQL container with authentication fix...
docker-compose restart mydb
echo.
echo Waiting for MySQL to be ready...
timeout /t 10 /nobreak > nul
echo.
echo Applying authentication fix...
docker exec -i lemp-mysql mysql -u root -p"rootpassword" < mysql\init\01-fix-auth-plugin.sql
echo.
echo MySQL authentication fix applied successfully!
goto end

:reset_mysql
echo.
echo WARNING: This will delete ALL MySQL data!
set /p confirm=Are you sure? Type 'yes' to continue: 
if not "%confirm%"=="yes" (
    echo Operation cancelled.
    goto end
)
echo.
echo Stopping containers...
docker-compose down
echo.
echo Removing MySQL data...
rmdir /s /q mysql\data 2>nul
echo.
echo Starting containers with fresh MySQL...
docker-compose up -d
echo.
echo Fresh MySQL installation completed with authentication fix!
goto end

:check_auth
echo.
echo Checking current authentication plugins...
docker exec -i lemp-mysql mysql -u root -p"rootpassword" -e "SELECT user, host, plugin FROM mysql.user WHERE user IN ('root', 'lemp_user');"
goto end

:test_connection
echo.
echo Testing database connection...
docker exec -i lemp-mysql mysql -u lemp_user -p"userpassword" lemp_db -e "SELECT 'Connection successful!' AS status;"
goto end

:view_logs
echo.
echo Viewing MySQL container logs (last 50 lines)...
docker logs --tail 50 lemp-mysql
goto end

:end
echo.
echo Operation completed.
pause

:exit
echo.
echo Goodbye!