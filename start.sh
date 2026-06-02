#!/bin/sh
php artisan schedule:work &
php -S 0.0.0.0:${PORT:-8080} -t public
