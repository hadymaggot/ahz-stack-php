#!/bin/bash
# AHZ Stack PHP - MySQL Authentication Fix Script
# This script helps fix MySQL authentication plugin issues

echo "========================================"
echo "AHZ Stack PHP - MySQL Authentication Fix"
echo "========================================"
echo

echo "Checking Docker containers status..."
docker-compose ps
echo

while true; do
    echo "Options:"
    echo "1. Restart MySQL with authentication fix (preserves data)"
    echo "2. Reset MySQL completely (WARNING: deletes all data)"
    echo "3. Check current authentication plugins"
    echo "4. Test database connection"
    echo "5. View MySQL logs"
    echo "6. Exit"
    echo
    
    read -p "Enter your choice (1-6): " choice
    
    case $choice in
        1)
            echo
            echo "Restarting MySQL container with authentication fix..."
            docker-compose restart mydb
            echo
            echo "Waiting for MySQL to be ready..."
            sleep 10
            echo
            echo "Applying authentication fix..."
            docker exec -i lemp-mysql mysql -u root -p"rootpassword" < mysql/init/01-fix-auth-plugin.sql
            echo
            echo "MySQL authentication fix applied successfully!"
            break
            ;;
        2)
            echo
            echo "WARNING: This will delete ALL MySQL data!"
            read -p "Are you sure? Type 'yes' to continue: " confirm
            if [ "$confirm" != "yes" ]; then
                echo "Operation cancelled."
                break
            fi
            echo
            echo "Stopping containers..."
            docker-compose down
            echo
            echo "Removing MySQL data..."
            rm -rf mysql/data
            echo
            echo "Starting containers with fresh MySQL..."
            docker-compose up -d
            echo
            echo "Fresh MySQL installation completed with authentication fix!"
            break
            ;;
        3)
            echo
            echo "Checking current authentication plugins..."
            docker exec -i lemp-mysql mysql -u root -p"rootpassword" -e "SELECT user, host, plugin FROM mysql.user WHERE user IN ('root', 'lemp_user');"
            echo
            ;;
        4)
            echo
            echo "Testing database connection..."
            docker exec -i lemp-mysql mysql -u lemp_user -p"userpassword" lemp_db -e "SELECT 'Connection successful!' AS status;"
            echo
            ;;
        5)
            echo
            echo "Viewing MySQL container logs (last 50 lines)..."
            docker logs --tail 50 lemp-mysql
            echo
            ;;
        6)
            echo
            echo "Goodbye!"
            exit 0
            ;;
        *)
            echo "Invalid choice. Please try again."
            echo
            ;;
    esac
done

echo
echo "Operation completed."
read -p "Press Enter to continue..."