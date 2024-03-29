ARG NODE_VERSION=20.11

FROM node:${NODE_VERSION}

WORKDIR /var/www/chess-empire/frontend

COPY package*.json ./

RUN npm install

RUN npm cache clean -f
RUN npm cache verify
RUN npm install -g react-scripts
#RUN npm audit fix

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]