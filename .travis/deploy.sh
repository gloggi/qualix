#!/bin/bash
set -e

rm -f .env
cp .env.example .env

sed -ri "s~^APP_ENV=.*$~APP_ENV=${APP_ENV:-production}~" .env
sed -ri "s~^APP_KEY=.*$~APP_KEY=$APP_KEY~" .env
sed -ri "s~^APP_DEBUG=.*$~APP_DEBUG=${APP_DEBUG:-false}~" .env
sed -ri "s~^APP_URL=.*$~APP_URL=$APP_URL~" .env

sed -ri "s~^DB_HOST=.*$~DB_HOST=${DB_HOST:-localhost}~" .env
sed -ri "s~^DB_DATABASE=.*$~DB_DATABASE=${DB_DATABASE:-qualix}~" .env
sed -ri "s~^DB_USERNAME=.*$~DB_USERNAME=$DB_USERNAME~" .env
sed -ri "s~^DB_PASSWORD=.*$~DB_PASSWORD=$DB_PASSWORD~" .env

sed -ri "s~^MAIL_HOST=.*$~MAIL_HOST=${MAIL_HOST:-localhost}~" .env
sed -ri "s~^MAIL_PORT=.*$~MAIL_PORT=${MAIL_PORT:-1025}~" .env
sed -ri "s~^MAIL_USERNAME=.*$~MAIL_USERNAME=$MAIL_USERNAME~" .env
sed -ri "s~^MAIL_PASSWORD=.*$~MAIL_PASSWORD=$MAIL_PASSWORD~" .env
sed -ri "s~^MAIL_ENCRYPTION=.*$~MAIL_ENCRYPTION=$MAIL_ENCRYPTION~" .env

sed -ri "s~^HITOBITO_BASE_URL=.*$~HITOBITO_BASE_URL=${HITOBITO_BASE_URL:-https://pbs.puzzle.ch}~" .env
sed -ri "s~^HITOBITO_CLIENT_UID=.*$~HITOBITO_CLIENT_UID=$HITOBITO_CLIENT_UID~" .env
sed -ri "s~^HITOBITO_CLIENT_SECRET=.*$~HITOBITO_CLIENT_SECRET=$HITOBITO_CLIENT_SECRET~" .env
sed -ri "s~^HITOBITO_CALLBACK_URI=.*$~HITOBITO_CALLBACK_URI=${HITOBITO_CALLBACK_URI:-${APP_URL}/login/hitobito/callback}~" .env

sed -ri "s~^SENTRY_LARAVEL_DSN=.*$~SENTRY_LARAVEL_DSN=${SENTRY_LARAVEL_DSN:-null}~" .env

docker-compose run --entrypoint "composer install --no-dev" app
docker-compose run --entrypoint "/bin/sh -c 'npm install && npm run prod'" node

# Travis CI uses OpenSSL 1.0.2g  1 Mar 2016. Files encrypted with newer versions of OpenSSL are not decryptable by
# the Travis CI version, error message is "bad decrypt". So to encrypt a file, use the following:
# docker run --rm -v $(pwd):/app -w /app frapsoft/openssl aes-256-cbc -k "<password>" -in <input_file> -out <output_file>
openssl aes-256-cbc -k "$ID_RSA_PASSWORD" -in .travis/id_rsa.enc -out .travis/id_rsa -d
eval "$(ssh-agent -s)"
chmod 600 .travis/id_rsa
ssh-add .travis/id_rsa

echo "Uploading files to the server..."
lftp <<EOF
  set sftp:auto-confirm true
  set dns:order "inet"
  open -u $SSH_USERNAME, sftp://$SSH_HOST
  cd $SSH_DIRECTORY
  mirror -enRv -x '^node_modules' -x '^\.' -x '^tests' -x '^storage/logs/.*' -x '^storage/app/.*'
  mirror -Rv -f .env
EOF

echo "All files uploaded to the server."

ssh -l $SSH_USERNAME -T $SSH_HOST <<EOF
  cd $SSH_DIRECTORY
  php artisan storage:link
  php artisan migrate --force
EOF
