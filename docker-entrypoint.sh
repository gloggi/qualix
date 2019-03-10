#!/bin/bash

composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

apache2-foreground
