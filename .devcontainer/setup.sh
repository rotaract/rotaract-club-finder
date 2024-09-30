#!/bin/bash
set -e
apt-get update
apt-get install -y curl unzip
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
mv wp-cli.phar /usr/local/bin/wp
cd /var/www/html/wp-content/plugins/rotaract-club-finder
composer install
#sudo -u www-data -i -- wp plugin activate rotaract-club-finder
