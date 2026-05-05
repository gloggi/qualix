#!/bin/sh

set -e

if [ $# -eq 0 ]; then
    echo "No arguments provided. Not starting the e2e tests."
    echo "To launch the Playwright UI, use 'docker compose run e2e open'"
    echo "To run the e2e tests in headless mode, use 'docker compose run e2e run'"
    exit 0;
fi

npm install

echo "Waiting for the app to be ready."
until curl -sf "${APP_BASE_URL}/login" > /dev/null 2>&1; do sleep 1; done
echo "App is ready!"

if [ "$1" = "open" ]; then
    exec npx playwright test --ui-host=0.0.0.0 --ui-port=1100
elif [ "$1" = "run" ]; then
    shift
    exec npx playwright test --project=firefox "$@"
else
    exec npx playwright "$@"
fi
