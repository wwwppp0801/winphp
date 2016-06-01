#rm -g winphp.tgz
#tar czvf winphp.tgz *

#sudo docker rmi winphp-php-fpm
#sudo docker build --force-rm -f php-fpm.docker -t winphp-php-fpm .

sudo docker rmi winphp-nginx
sudo docker build --force-rm  -f nginx.docker -t winphp-nginx .


