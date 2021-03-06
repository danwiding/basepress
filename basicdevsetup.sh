#!/bin/sh
THEMEDIR="$( cd "$( dirname "$0" )" && cd ".." && pwd )"

sh "${THEMEDIR}/basepress/setup.sh"


#htaccess
ln -nfs ${THEMEDIR}/config/htaccess/.htaccess-dev ${THEMEDIR}/basepress/wordpress/.htaccess
#wp-config
ln -nfs ${THEMEDIR}/config/wordpress-app/wp-config-dev.php ${THEMEDIR}/config/wordpress-app/wp-config-local.php
#capistrano files for the theme
unlink ${THEMEDIR}/basepress/capistrano/config/deploy
ln -nfs ${THEMEDIR}/config/capistrano-deploy ${THEMEDIR}/basepress/capistrano/config/deploy

ln -nfs ${THEMEDIR}/basepress/lib/php-object-generator/objects/class.database.php ${THEMEDIR}/basepress/lib/php-object-generator/RunTimeFiles/class.database.php
ln -nfs ${THEMEDIR}/basepress/lib/php-object-generator/objects/class.pog_base.php ${THEMEDIR}/basepress/lib/php-object-generator/RunTimeFiles/class.pog_base.php
