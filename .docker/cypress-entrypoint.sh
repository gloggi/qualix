#!/bin/sh

set -e

echo "💤 Waiting for the app to be ready."
npx wait-on "${APP_BASE_URL}/login"
echo "✅ App is ready!"

echo "➡️ Handing control over to 'cypress $*'"

exec cypress "$@" --project .
