FROM node:20-alpine

WORKDIR /app

# Copy the current directory contents into the container at /usr/src/app
COPY . .

RUN npm install && \
  npm install --save-dev babel-plugin-module-resolver customize-cra react-app-rewired @babel/plugin-proposal-private-property-in-object

EXPOSE 3000

CMD ["npm", "start"]