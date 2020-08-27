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

sed -ri "s~^SESSION_SECURE_COOKIE=.*$~SESSION_SECURE_COOKIE=true~" .env

sed -ri "s~^MAIL_HOST=.*$~MAIL_HOST=${MAIL_HOST:-localhost}~" .env
sed -ri "s~^MAIL_PORT=.*$~MAIL_PORT=${MAIL_PORT:-1025}~" .env
sed -ri "s~^MAIL_USERNAME=.*$~MAIL_USERNAME=$MAIL_USERNAME~" .env
sed -ri "s~^MAIL_PASSWORD=.*$~MAIL_PASSWORD=$MAIL_PASSWORD~" .env
sed -ri "s~^MAIL_ENCRYPTION=.*$~MAIL_ENCRYPTION=$MAIL_ENCRYPTION~" .env
sed -ri "s~^MAIL_FROM_ADDRESS=.*$~MAIL_FROM_ADDRESS=$MAIL_FROM_ADDRESS~" .env

sed -ri "s~^HITOBITO_BASE_URL=.*$~HITOBITO_BASE_URL=${HITOBITO_BASE_URL:-https://pbs.puzzle.ch}~" .env
sed -ri "s~^HITOBITO_CLIENT_UID=.*$~HITOBITO_CLIENT_UID=$HITOBITO_CLIENT_UID~" .env
sed -ri "s~^HITOBITO_CLIENT_SECRET=.*$~HITOBITO_CLIENT_SECRET=$HITOBITO_CLIENT_SECRET~" .env
sed -ri "s~^HITOBITO_CALLBACK_URI=.*$~HITOBITO_CALLBACK_URI=${HITOBITO_CALLBACK_URI:-${APP_URL}/login/hitobito/callback}~" .env

sed -ri "s~^SENTRY_LARAVEL_DSN=.*$~SENTRY_LARAVEL_DSN=${SENTRY_LARAVEL_DSN:-null}~" .env
sed -ri "s~^SENTRY_USER_FEEDBACK_URL=.*$~SENTRY_USER_FEEDBACK_URL=$SENTRY_USER_FEEDBACK_URL~" .env
sed -ri "s~^SENTRY_CSP_REPORT_URI=.*$~SENTRY_CSP_REPORT_URI=$SENTRY_CSP_REPORT_URI~" .env

docker-compose run --entrypoint "composer install --no-dev" app
docker-compose run --entrypoint "/bin/sh -c 'npm install && npm run prod --no-unsafe-inline'" node

# Travis CI uses OpenSSL 1.0.2g  1 Mar 2016. Files encrypted with newer versions of OpenSSL are not decryptable by
# the Travis CI version, error message is "bad decrypt". So to encrypt a file, use the following:
# docker run --rm -v $(pwd):/app -w /app frapsoft/openssl aes-256-cbc -k "<password>" -in <input_file> -out <output_file>
openssl aes-256-cbc -k "$ID_RSA_PASSWORD" -in .travis/id_rsa.enc -out .travis/id_rsa -d
eval "$(ssh-agent -s)"
chmod 600 .travis/id_rsa
ssh-add .travis/id_rsa

# Add fingerprint of server to known hosts
echo "|1|JR7fpL0gLKe8icyVQtx89E3xKA0=|fWzxmKWZG+dr2Q+7aGePHZEYcgA= ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIKlHpAA/T87DCjPTHb2o5nLuxfPDhj00cZB2lBlNjbbb" >> ~/.ssh/known_hosts
echo "|1|kwYauc4WMSDAwXG/SfFoYNwYYnM=|xu3ceXG8okp44TfyR6h56godaLQ= ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIKlHpAA/T87DCjPTHb2o5nLuxfPDhj00cZB2lBlNjbbb" >> ~/.ssh/known_hosts
echo "|1|tkWQ7CJXd4LxeV0L/T6usElIEMk=|KPrqJZWpOb3tNR+nm4/u6KXSiFU= ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBIKVCZ0dYk2R0cKd7/hDY4vOTkCm4vmdwtV9jhoWLff70uCAyVbYQ0qReRn/zQY15jbJmr7U84vYHwipUcndBc0=" >> ~/.ssh/known_hosts
echo "|1|HkwzaiV9MewbGGd+CNmnUwShkD0=|w2gSII2hnyxvIX24RxdwXB94rhQ= ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBIKVCZ0dYk2R0cKd7/hDY4vOTkCm4vmdwtV9jhoWLff70uCAyVbYQ0qReRn/zQY15jbJmr7U84vYHwipUcndBc0=" >> ~/.ssh/known_hosts
echo "|1|AKBTB7xthFG7AL4DDjk70zQc+Pg=|6pMFoR9FwtTtfDOmpP1Ziudngx0= ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBKSQ6V3VRAg8jxrM/LGkSQzCrhLa83DV3rYIIuUchzrhal8q12Ab0GERYy5Suaqmj11ydna7CkN8uSs757PTB6g=" >> ~/.ssh/known_hosts
echo "|1|dfj9V26TOanb1MT539vM0ttx21s=|5bdR/2oxiaBU8Uh1CMVu/vwX5VA= ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBKSQ6V3VRAg8jxrM/LGkSQzCrhLa83DV3rYIIuUchzrhal8q12Ab0GERYy5Suaqmj11ydna7CkN8uSs757PTB6g=" >> ~/.ssh/known_hosts

echo "Checking PHP version"
ssh -l $SSH_USERNAME -T $SSH_HOST <<EOF
  set -e
  php -v
  cd $SSH_DIRECTORY
  php -r "if((explode('.',PHP_VERSION)[0]*10000+explode('.',PHP_VERSION)[1]*100+explode('.',PHP_VERSION)[2])<${PHP_MIN_VERSION:-70205}){echo \"Your PHP version is too old\\n\";exit(1);}"
EOF

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
