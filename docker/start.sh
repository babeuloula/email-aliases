#!/usr/bin/env bash

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))
cd ${DOCKER_PATH};

. ./lib/functions.sh
parse_env ".env.dist" ".env"
. ./.env

# Build all container in parallel to optimize your time
docker compose build --parallel

# Start and remove useless containers
docker compose up -d --remove-orphans
