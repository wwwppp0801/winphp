sudo docker run -d --name winphp-php-fpm wwwppp0801@gmail.com/winphp-php-fpm /usr/sbin/php-fpm7.0 -F
sudo docker run -d --name winphp-nginx --link winphp-php-fpm:php-fpm.server wwwppp0801@gmail.com/winphp-nginx /usr/sbin/nginx -c /root/winphp/nginx.conf -g "daemon off;" 
