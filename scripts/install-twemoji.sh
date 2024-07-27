#!/usr/bin/env sh

# We need twemoji for emoji support in PDFs. Since there is no maintained npm package
# which includes the actual emoji images, we use this script to install the images. By default,
# twemoji are usually accessed from CDNs, but for traceability reasons, we self-host them.

set -euo

if [ ! -d "resources/twemoji" ]
then
  echo 'downloading twemoji images from GitHub...'
  # Clone as little as possible. No past revisions and only the image files we are interested in.
  git clone --depth 1 --no-checkout --filter=blob:none https://github.com/twitter/twemoji.git resources/twemoji
  cd resources/twemoji
  git checkout master
else
  echo 'twemoji are already present, updating them to the latest version...'
  cd resources/twemoji
  git pull || echo 'Could not update twemoji. Skipping for now...'
fi

echo 'twemoji images should be up to date now.'
