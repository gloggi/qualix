#!/bin/bash
set -e

rm -f .env
cp .env.example .env

if [ "$APP_NAME" != "" ]; then
  sed -ri "s~^#APP_NAME=.*$~APP_NAME=$APP_NAME~" .env
fi

sed -ri "s~^APP_ENV=.*$~APP_ENV=$APP_ENV~" .env
sed -ri "s~^APP_KEY=.*$~APP_KEY=$APP_KEY~" .env
sed -ri "s~^APP_DEBUG=.*$~APP_DEBUG=$APP_DEBUG~" .env
sed -ri "s~^APP_URL=.*$~APP_URL=$APP_URL~" .env
sed -ri "s~^APP_CONTACT_LINK=.*$~APP_CONTACT_LINK=$APP_CONTACT_LINK~" .env

if [ "$APP_CONTACT_TEXT" != "" ]; then
  sed -ri "s~^#APP_CONTACT_TEXT=.*$~APP_CONTACT_TEXT=$APP_CONTACT_TEXT~" .env
fi

sed -ri "s~^DB_HOST=.*$~DB_HOST=$DB_HOST~" .env
sed -ri "s~^DB_DATABASE=.*$~DB_DATABASE=$DB_DATABASE~" .env
sed -ri "s~^DB_USERNAME=.*$~DB_USERNAME=$DB_USERNAME~" .env
sed -ri "s~^DB_PASSWORD=.*$~DB_PASSWORD=$DB_PASSWORD~" .env

sed -ri "s~^SESSION_SECURE_COOKIE=.*$~SESSION_SECURE_COOKIE=true~" .env

if [ "$MAIL_MAILER" = "sendmail" ]; then
  sed -ri "s~^MAIL_MAILER=.*$~MAIL_MAILER=$MAIL_MAILER~" .env

  sed -ri "s~^MAIL_HOST=.*$~~" .env
  sed -ri "s~^MAIL_PORT=.*$~~" .env
  sed -ri "s~^MAIL_USERNAME=.*$~~" .env
  sed -ri "s~^MAIL_PASSWORD=.*$~~" .env
  sed -ri "s~^MAIL_ENCRYPTION=.*$~~" .env
else
  sed -ri "s~^MAIL_HOST=.*$~MAIL_HOST=$MAIL_HOST~" .env
  sed -ri "s~^MAIL_PORT=.*$~MAIL_PORT=$MAIL_PORT~" .env
  sed -ri "s~^MAIL_USERNAME=.*$~MAIL_USERNAME=$MAIL_USERNAME~" .env
  sed -ri "s~^MAIL_PASSWORD=.*$~MAIL_PASSWORD=$MAIL_PASSWORD~" .env
  sed -ri "s~^MAIL_ENCRYPTION=.*$~MAIL_ENCRYPTION=$MAIL_ENCRYPTION~" .env
fi
sed -ri "s~^MAIL_FROM_ADDRESS=.*$~MAIL_FROM_ADDRESS=$MAIL_FROM_ADDRESS~" .env

sed -ri "s~^HITOBITO_BASE_URL=.*$~HITOBITO_BASE_URL=$HITOBITO_BASE_URL~" .env
sed -ri "s~^HITOBITO_CLIENT_UID=.*$~HITOBITO_CLIENT_UID=$HITOBITO_CLIENT_UID~" .env
sed -ri "s~^HITOBITO_CLIENT_SECRET=.*$~HITOBITO_CLIENT_SECRET=$HITOBITO_CLIENT_SECRET~" .env
sed -ri "s~^HITOBITO_CALLBACK_URI=.*$~HITOBITO_CALLBACK_URI=${APP_URL}/login/hitobito/callback~" .env

sed -ri "s~^COLLABORATION_ENABLED=.*$~COLLABORATION_ENABLED=${COLLABORATION_ENABLED}~" .env
sed -ri "s~^COLLABORATION_SIGNALING_SERVERS=.*$~COLLABORATION_SIGNALING_SERVERS=${COLLABORATION_SIGNALING_SERVERS}~" .env

sed -ri "s~^SENTRY_RELEASE=.*$~SENTRY_RELEASE=$(git rev-parse HEAD)~" .env
sed -ri "s~^SENTRY_LARAVEL_DSN=.*$~SENTRY_LARAVEL_DSN=$SENTRY_LARAVEL_DSN~" .env
sed -ri "s~^SENTRY_CSP_REPORT_URI=.*$~SENTRY_CSP_REPORT_URI=$SENTRY_CSP_REPORT_URI~" .env
sed -ri "s~^MIX_SENTRY_VUE_DSN=.*$~MIX_SENTRY_VUE_DSN=$SENTRY_VUE_DSN~" .env

docker compose run --no-deps --entrypoint "/bin/sh -c 'npm install && scripts/install-twemoji.sh && npm run prod --no-unsafe-inline'" node
docker compose run --no-deps --entrypoint "composer install --no-dev" qualix
PHP_MIN_VERSION_ID=$(grep -Po '(?<=\(PHP_VERSION_ID >= )[0-9]+(?=\))' vendor/composer/platform_check.php)

echo "Scanning ssh host keys of \"$SSH_HOST\" (showing hashed output only):"
ssh-keyscan -H $SSH_HOST

echo "Showing configured know_hosts:"
cat ~/.ssh/known_hosts

echo "Checking PHP version:"
ssh -l $SSH_USERNAME -T $SSH_HOST <<EOF
  set -e
  php -v
  cd $SSH_DIRECTORY
  php -r "if(PHP_VERSION_ID<${PHP_MIN_VERSION_ID:-80200}){echo \"Your PHP version is too old\\nYou might be able to use these instructions on your hosting as well: https://www.cyon.ch/support/a/php-standardversion-fur-die-kommandozeile-festlegen\n\";exit(1);}"

  APP_CONTACT_LINK=$APP_CONTACT_LINK php artisan down --render=updating
EOF

echo "Creating deployment package..."
DEPLOY_FILE="deploy_package.zip"
rm -f $DEPLOY_FILE
zip -r $DEPLOY_FILE . -x "node_modules/*" "tests/*" "cypress/*" ".git/*" ".github/*" "storage/app/*" "storage/logs/*" "storage/framework/maintenance.php" "storage/framework/down" "resources/fonts/*" "resources/images/*" "resources/js/*" "resources/sass/*" "resources/twemoji/*"

echo "Uploading zip and .env to server..."
scp $DEPLOY_FILE $SSH_USERNAME@$SSH_HOST:$SSH_DIRECTORY/
scp .env $SSH_USERNAME@$SSH_HOST:$SSH_DIRECTORY/


echo "Final server side setup..."
ssh -l $SSH_USERNAME -T $SSH_HOST <<EOF
  cd $SSH_DIRECTORY
  unzip -o $DEPLOY_FILE
  rm -f $DEPLOY_FILE
  php artisan storage:link
  php artisan migrate --force

  php artisan config:cache
  php artisan route:cache
  php artisan view:cache

  php artisan up
EOF
