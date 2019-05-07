#!/usr/bin/env bash

# Wait for frontend NPM build to finish
while [ ! -f ./public/js/app.js ]
do
  sleep 2
done

# Wait for backend composer install and DB migration to finish
while [ ! -f ./.ready ]
do
  sleep 2
done
