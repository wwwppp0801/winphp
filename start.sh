#!/bin/bash
PWD=$(pwd)
ROOT_PATH=$PWD/$(dirname $0)

APP_PATH=$PWD/$1
mkdir -p $APP_PATH
cd $APP_PATH
mkdir -p {ctemplates,log,template,webroot/upload,config,app/controller}
chmod -R 777 ctemplates log webroot/upload
cp $ROOT_PATH/webroot/route.php webroot/
cp $ROOT_PATH/config/conf.php.dist config/conf.php
cp $ROOT_PATH/controller/IndexController.class.php config/conf.php
cd -
