#!/bin/bash

cd /home/ps/dev/sfta

rm -rf nginx/build

cd frontend && sudo rm -rf build

sudo npm run build

cd .. && cp -r frontend/build nginx/

./upload.sh nginx

echo "Frontend redeployed successfully."