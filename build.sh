rm -g winphp.tgz
tar czvf winphp.tgz *
#sudo docker build -f nginx.docker wwwppp0801@gmail.com/winphp-nginx
sudo docker build -f php-fpm.docker -t winphp-php-fpm .
