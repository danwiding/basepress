#!/bin/sh
THEMEDIR="$( cd "$( dirname "$0" )" && cd ".." && pwd )"

ln -nfs ${THEMEDIR}/plugins ${THEMEDIR}/juntobasepress/wordpress/wp-content/plugins
ln -nfs ${THEMEDIR}/themes ${THEMEDIR}/juntobasepress/wordpress/wp-content/themes

mkdir ${THEMEDIR}/juntobasepress/wordpress/wp-content/blogs.dir
mkdir ${THEMEDIR}/juntobasepress/wordpress/wp-content/uploads

