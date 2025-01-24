#!/bin/bash
composer i

while true; do
  php bin/console d:m:m --no-interaction && break
  # Attend quelques secondes entre les tentatives
  sleep 5s
done

php bin/console d:f:l --append

while true; do
  php bin/console d:m:m -n --env=test && break
  # Attend quelques secondes entre les tentatives
  sleep 5s
done

npm i && npm run build

php bin/phpunit --coverage-html var/coverage

# Une fois la migration réussie, démarrez php-fpm
php-fpm
