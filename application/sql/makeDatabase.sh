#!/bin/bash

OS_NAME=`uname`

if [[ $OS_NAME = 'Linux' ]]; then
	MYSQL="/usr/bin/mysql"
else
	MYSQL="/usr/local/mysql/bin/mysql"
fi

NAME="StaffRoster"

echo " DROP DATABASE IF EXISTS $NAME; CREATE DATABASE $NAME; GRANT ALL PRIVILEGES ON $NAME.* TO $NAME@localhost IDENTIFIED BY '$NAME'; " | $MYSQL -u root
$MYSQL -u $NAME --password=$NAME $NAME < build.sql
php ../../index.php admin/login/reset > /dev/null 2>&1


