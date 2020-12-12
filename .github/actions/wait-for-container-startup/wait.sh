#!/usr/bin/env bash

echo "Waiting for frontend container to start up..."
while [ ! -f ./public/js/app.js ]
do
  sleep 2
done
echo "Frontend container is ready."

echo "Waiting for backend container to start up and migrate DB..."
while [ ! -f ./.ready ]
do
  sleep 2
done
echo "Backend container is ready."
