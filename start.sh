#!/bin/bash
PWD=$(pwd)
WINPHP_PATH=$PWD/$(dirname $0)

APP_PATH=$PWD/$1
mkdir -p $APP_PATH
cd $APP_PATH
mkdir -p {ctemplates,log,template,webroot/upload,config,app/controller}
chmod -R 777 ctemplates log webroot/upload
cp $WINPHP_PATH/webroot/route.php webroot/
cp $WINPHP_PATH/config/conf.php.dist config/conf.php
cp $WINPHP_PATH/template/sidebar.tpl template/sidebar.tpl
ln -s $WINPHP_PATH/webroot $APP_PATH/webroot/winphp
cd -
