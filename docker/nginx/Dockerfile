FROM nginx:latest

ADD conf.d/nginx_local.conf /etc/nginx/conf.d/default.template

CMD sh -c "envsubst \"`env | awk -F = '{printf \" \\\\$%s\", $1}'`\" < /etc/nginx/conf.d/default.template > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'"
