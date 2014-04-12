#!/bin/sh

ROOT_PATH=$(dirname $0)
mkdir -p $ROOT_PATH/{ctemplates,log,template,webroot/upload}
chmod -R 777   ctemplates log webroot/upload
