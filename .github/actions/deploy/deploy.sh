#!/bin/bash
set -e

rm -f .env
cp .env.example .env

sed -ri "s~^APP_ENV=.*$~APP_ENV=$APP_ENV~" .env
sed -ri "s~^APP_KEY=.*$~APP_KEY=$APP_KEY~" .env
sed -ri "s~^APP_DEBUG=.*$~APP_DEBUG=$APP_DEBUG~" .env
sed -ri "s~^APP_URL=.*$~APP_URL=$APP_URL~" .env
sed -ri "s~^APP_CONTACT_LINK=.*$~APP_CONTACT_LINK=$APP_CONTACT_LINK~" .env

sed -ri "s~^DB_HOST=.*$~DB_HOST=$DB_HOST~" .env
sed -ri "s~^DB_DATABASE=.*$~DB_DATABASE=$DB_DATABASE~" .env
sed -ri "s~^DB_USERNAME=.*$~DB_USERNAME=$DB_USERNAME~" .env
sed -ri "s~^DB_PASSWORD=.*$~DB_PASSWORD=$DB_PASSWORD~" .env

sed -ri "s~^SESSION_SECURE_COOKIE=.*$~SESSION_SECURE_COOKIE=true~" .env

sed -ri "s~^MAIL_HOST=.*$~MAIL_HOST=$MAIL_HOST~" .env
sed -ri "s~^MAIL_PORT=.*$~MAIL_PORT=$MAIL_PORT~" .env
sed -ri "s~^MAIL_USERNAME=.*$~MAIL_USERNAME=$MAIL_USERNAME~" .env
sed -ri "s~^MAIL_PASSWORD=.*$~MAIL_PASSWORD=$MAIL_PASSWORD~" .env
sed -ri "s~^MAIL_ENCRYPTION=.*$~MAIL_ENCRYPTION=$MAIL_ENCRYPTION~" .env
sed -ri "s~^MAIL_FROM_ADDRESS=.*$~MAIL_FROM_ADDRESS=$MAIL_FROM_ADDRESS~" .env

sed -ri "s~^HITOBITO_BASE_URL=.*$~HITOBITO_BASE_URL=$HITOBITO_BASE_URL~" .env
sed -ri "s~^HITOBITO_CLIENT_UID=.*$~HITOBITO_CLIENT_UID=$HITOBITO_CLIENT_UID~" .env
sed -ri "s~^HITOBITO_CLIENT_SECRET=.*$~HITOBITO_CLIENT_SECRET=$HITOBITO_CLIENT_SECRET~" .env
sed -ri "s~^HITOBITO_CALLBACK_URI=.*$~HITOBITO_CALLBACK_URI=${APP_URL}/login/hitobito/callback~" .env

sed -ri "s~^SENTRY_LARAVEL_DSN=.*$~SENTRY_LARAVEL_DSN=$SENTRY_LARAVEL_DSN~" .env
sed -ri "s~^SENTRY_USER_FEEDBACK_URL=.*$~SENTRY_USER_FEEDBACK_URL=$SENTRY_USER_FEEDBACK_URL~" .env
sed -ri "s~^SENTRY_CSP_REPORT_URI=.*$~SENTRY_CSP_REPORT_URI=$SENTRY_CSP_REPORT_URI~" .env
sed -ri "s~^MIX_SENTRY_VUE_DSN=.*$~MIX_SENTRY_VUE_DSN=$SENTRY_VUE_DSN~" .env

docker-compose run --no-deps --entrypoint "/bin/sh -c 'npm install && npm run prod --no-unsafe-inline'" node
docker-compose run --no-deps --entrypoint "composer install --no-dev" qualix
PHP_MIN_VERSION_ID=$(grep -Po '(?<=\(PHP_VERSION_ID >= )[0-9]+(?=\))' vendor/composer/platform_check.php)

cat ~/.ssh/known_hosts

echo "Checking PHP version"
ssh -l $SSH_USERNAME -T $SSH_HOST -o StrictHostKeyChecking=no <<EOF
  set -e
  php -v
  cd $SSH_DIRECTORY
  php -r "if(PHP_VERSION_ID<${PHP_MIN_VERSION_ID:-70400}){echo \"Your PHP version is too old\\n\";exit(1);}"
EOF

cat ~/.ssh/known_hosts

echo "Uploading files to the server..."
lftp <<EOF
  set sftp:auto-confirm true
  set dns:order "inet"
  open -u $SSH_USERNAME, sftp://$SSH_HOST
  cd $SSH_DIRECTORY
  mirror -enRv -x '^node_modules' -x '^cypress' -x '^\.' -x '^tests' -x '^storage/logs/.*' -x '^storage/app/.*'
  mirror -Rv -f .env
EOF

echo "All files uploaded to the server."

ssh -l $SSH_USERNAME -T $SSH_HOST <<EOF
  cd $SSH_DIRECTORY
  php artisan storage:link
  php artisan migrate --force
EOF
