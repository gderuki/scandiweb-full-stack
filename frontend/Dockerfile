FROM node:14

WORKDIR /usr/src/app

# Copy the current directory contents into the container at /usr/src/app
COPY . .

RUN npm install

EXPOSE 3000

CMD ["npm", "start"]