FROM nginx:alpine

COPY config/nginx.conf.PROD.example /etc/nginx/nginx.conf
COPY ./build /var/www/

EXPOSE 80

CMD [ "nginx", "-g", "daemon off;" ]