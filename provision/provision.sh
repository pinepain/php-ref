#!/bin/bash

echo Provisioning...
sudo apt-get update
sudo apt-get -y autoremove

# Make sure these tools installed
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y git htop curl tshark pkgconf
# Add PPA with fresh PHP 7:
sudo add-apt-repository -u -y ppa:ondrej/php-7.0

# Install available php from packages
sudo apt-get install -y php7.0 php7.0-dev php7.0-fpm

# Configure php-fpm
sudo cp ~/php-weak/provision/php/www.conf /etc/php/7.0/fpm/pool.d/www.conf
sudo service php7.0-fpm restart

# Fix php executable detection with PHP7 (https://github.com/oerdnj/deb.sury.org/issues/142)
sudo sed -i -e 's/^php_cli_binary=NONE$/php_cli_binary="\/usr\/bin\/php"/g' /usr/bin/php-config

# Install phpbrew to manage php versions
curl -L -O -s https://github.com/phpbrew/phpbrew/raw/master/phpbrew
chmod +x phpbrew
sudo mv phpbrew /usr/bin/phpbrew
phpbrew init

cp ~/php-weak/provision/.bashrc ~/.bashrc

sudo mkdir -p /var/www/html/
sudo chown -R vagrant:vagrant /var/www

# Requirements to build php from sources, uncomment if you decide to do so
#sudo apt-get install -y \
#    libxml2-dev \
#    libcurl4-openssl-dev \
#    libjpeg-dev \
#    libpng-dev \
#    libxpm-dev \
#    libmcrypt-dev \
#    libmysqlclient-dev \
#    libpq-dev \
#    libicu-dev \
#    libfreetype6-dev \
#    libldap2-dev \
#    libxslt-dev \
#    libbz2-dev \
#    libreadline-dev \
#    autoconf \
#    libtool \
#    pkg-config

# Valgrind for investigating and plumbing memory-related problems
sudo apt-get install valgrind

# Benchmarking...
sudo apt-get install -y apache2-utils
# For Apache-based installation
sudo apt-get install -y apache2 libapache2-mod-php

# Move Apache to port 8080
sudo cp ~/php-weak/provision/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
sudo cp ~/php-weak/provision/apache/ports.conf /etc/apache2/ports.conf
sudo service apache2 restart

sudo cp -f /var/www/html/index.html /var/www/html/index-apache.html

sudo bash -c "echo '<?php phpinfo();' > /var/www/html/index.php"

# For Nginx-based installation
sudo apt-get install -y nginx
sudo cp ~/php-weak/provision/nginx/default /etc/nginx/sites-available/default
sudo service nginx restart
sudo cp -f /usr/share/nginx/html/index.html /var/www/html/index-nginx.html


# Do it manually when you need it,
#cd ~/php-weak
#phpize --clean && phpize && ./configure && sudo make install
#sudo cp ~/php-weak/provision/php/weak.ini /etc/php/mods-available/
#sudo phpenmod -v ALL weak
#sudo service php7.0-fpm restart

# For debugging segfault when extension fails in php-fpm mode:
#sudo sh -c "echo '/home/vagrant/php-weak/coredump-%e.%p' > /proc/sys/kernel/core_pattern"

# To test with typical dev configuration - with xdebug:
#sudo apt-get install -y php-xdebug

# Cleanup unused stuff
sudo apt-get autoremove -y

# This is for the future
# At this point it is good idea to do `phpbrew install 7.1` (or other version you want to test extension with)
# and `phpbrew ext install ~/php-weak/`

date > /home/vagrant/vagrant_provisioned_at
