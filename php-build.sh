#!/bin/bash
set -e
php-cs-fixer fix ./src --config=.php-cs-fixer54 
php ./Make.php build
echo '\nPlease access via http://localhost:8001/prober.php' 
PHP_CLI_SERVER_WORKERS=8  php -S localhost:8001 -t dist