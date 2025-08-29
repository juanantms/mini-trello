#!/bin/bash

# Quick setup script for SQLite configuration

echo "Setting up Mini-Trello with SQLite..."

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cat > .env << 'EOF'
APP_NAME="Mini-Trello Laravel"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# SQLite Database Configuration
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

SAIL_XDEBUG_MODE=develop,debug,coverage
EOF
fi

# Ensure SQLite database file exists
if [ ! -f database/database.sqlite ]; then
    echo "Creating SQLite database file..."
    touch database/database.sqlite
fi

echo "SQLite setup completed!"
echo ""
echo "To start the application:"
echo "1. Run: ./vendor/bin/sail up -d"
echo "2. Run: ./vendor/bin/sail artisan key:generate"
echo "3. Run: ./vendor/bin/sail artisan migrate"
echo "4. Run: ./vendor/bin/sail npm install && ./vendor/bin/sail npm run dev"
echo ""
echo "The application will be available at http://localhost"
