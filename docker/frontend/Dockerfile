FROM node:latest

WORKDIR /var/www/chess-empire/frontend

COPY package*.json ./

RUN npm install

RUN npm cache clean -f
RUN npm cache verify
RUN npm install -g react-scripts
#RUN npm audit fix