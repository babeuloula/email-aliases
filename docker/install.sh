#!/usr/bin/env bash

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))
cd ${DOCKER_PATH};

. ./lib/functions.sh

block_info "Welcome to Email aliases installer!"

check_requirements
parse_env ".env.dist" ".env"
. ./.env
echo -e "${GREEN}Configuration done!${RESET}" > /dev/tty

# Install SSL certificates for dev
./mkcert.sh

block_info "Build & start Docker"
# Pull all container in parallel to optimize your time
docker compose pull
./stop.sh
./start.sh
echo -e "${GREEN}Docker is started with success!${RESET}" > /dev/tty

block_info "Install dependencies"
install_composer
echo -e "${GREEN}Dependencies installed with success!${RESET}" > /dev/tty

add_host "${HTTP_HOST}"

#database_and_migrations

block_success "Email aliases is started https://${HTTP_HOST}"
