#!/bin/sh
THEMEDIR="$( cd "$( dirname "$0" )" && cd ".." && pwd )"

sh "${THEMEDIR}/juntobasepress/setup.sh"


#htaccess
ln -nfs ${THEMEDIR}/config/htaccess/.htaccess-dev ${THEMEDIR}/juntobasepress/wordpress/.htaccess
#wp-config
ln -nfs ${THEMEDIR}/config/wordpress-app/wp-config-dev.php ${THEMEDIR}/config/wordpress-app/wp-config-local.php
#capistrano files for the theme
rm ${THEMEDIR}/juntobasepress/capistrano/config/deploy
ln -nfs ${THEMEDIR}/config/capistrano-deploy ${THEMEDIR}/juntobasepress/capistrano/config/deploy
