#!/bin/bash
set -e
php ./Make.php dev 
PHP_CLI_SERVER_WORKERS=8 php -S localhost:8000 -t ./dev