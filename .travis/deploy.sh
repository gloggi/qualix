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

docker-compose run --entrypoint "/bin/sh -c 'composer install --no-dev'" app
docker-compose run --entrypoint "/bin/sh -c 'npm install && npm run prod'" node

openssl aes-256-cbc -k "$ID_RSA_PASSWORD" -in .travis/id_rsa.enc -out .travis/id_rsa -d
eval "$(ssh-agent -s)"
chmod 600 .travis/id_rsa
ssh-add .travis/id_rsa

echo "Uploading files to the server..."
lftp -c "set sftp:auto-confirm true; set dns:order \"inet\"; open -u $SFTP_USERNAME, sftp://$SFTP_HOST ; mirror -enRv -x .env -x node_modules -x .git -x tests -x .travis . $SFTP_DIRECTORY"
echo "All files uploaded to the server."

ssh -l $SFTP_USERNAME $SFTP_HOST <<EOF
  cd $SFTP_DIRECTORY
  php artisan route:clear
  php artisan config:clear
  php artisan cache:clear
  php artisan storage:link
  php artisan migrate --force
EOF
