#!/bin/sh

set -e

if [ $# -eq 0 ]; then
    echo "No arguments provided. Not starting the e2e tests."
    echo "To launch the cypress GUI, use 'docker compose run e2e open'"
    echo "To run the e2e tests in headless mode, use 'docker compose run e2e run'"
    exit 0;
fi

echo "💤Waiting for the app to be ready."
npx wait-on "${APP_BASE_URL}/login"
echo "App is ready!"

echo "Handing control over to 'cypress $* --project .'"

exec cypress "$@" --project .
