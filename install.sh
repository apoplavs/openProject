#!/bin/sh

echo "Checking dependencies"
# check if installed composer
COMPOSER=$(composer)
if $( echo $COMPOSER | grep --quiet 'Composer version')
then
    echo 'composer OK'
else
    echo 'composer not installed,\n install composer from https://getcomposer.org and try again'
    exit;
fi

# update composer
composer update --no-scripts

# check if installed laravel
LARAVEL=$(php artisan --version)
if $( echo $LARAVEL | grep --quiet 'Laravel')
then
    echo 'Laravel OK'
else
    echo 'Laravel not installed,\n install laravel https://laravel.com and try again'
    exit;
fi

# Setup variables
DATABASE="open_project"
# DIRECTORY_PROJECT=$(pwd)

echo "\n\n\n Enter username to MySQL"
read USERNAME
echo "Enter password to MySQL"
read PASS
echo "Enter host MySQL  ('127.0.0.1' if default)"
read HOST

echo "installation Database ...\n"
echo "it will take a few minutes\n"

mysql -u $USERNAME -p$PASS -h $HOST -e "SOURCE db_import/open_project.sql;"
SETDATABASE=$(mysql -u $USERNAME -p$PASS -N -h $HOST -D $DATABASE -e "SHOW TABLES;")
if $( echo $SETDATABASE | grep -q 'migrations')
then
	echo "Database installed successfully\n\n"
	exit;
else
	echo "Database installation error";
	exit;
fi

echo "Setup settings ..."
echo "APP_NAME=ТОЕсуд
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_URL=http://openproject.local

DB_CONNECTION=mysql
DB_HOST=$HOST
DB_PORT=3306
DB_DATABASE=$DATABASE
DB_USERNAME=$USERNAME
DB_PASSWORD=$PASS

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=" >> .env
echo "Settings installed successfully\n"

php artisan key:generate
php artisan optimize

#echo "Setting permissions.."
# chgrp -R www-data $DIRECTORY_PROJECT
#chmod -R 775 $DIRECTORY_PROJECT/storage

echo "Done. Restart apache, and try use it"
#echo "if something does not work    https://www.howtoforge.com/tutorial/install-laravel-on-ubuntu-for-apache"