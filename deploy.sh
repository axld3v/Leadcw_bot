#!/bin/bash

set -e

echo "START deploy";
php8.1 composer.phar install --no-dev --optimize-autoloader
php8.1 artisan migrate --force
php8.1 artisan app:create-webhook
echo "Done!";
