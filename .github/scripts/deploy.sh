#!/usr/bin/env bash

set -Eeuo pipefail

: "${DEPLOY_PATH:?DEPLOY_PATH is required}"
: "${RELEASE_ID:?RELEASE_ID is required}"
: "${ARCHIVE:?ARCHIVE is required}"

releases_path="${DEPLOY_PATH}/releases"
shared_path="${DEPLOY_PATH}/shared"
release_path="${releases_path}/${RELEASE_ID}"

if [[ ! -f "${shared_path}/.env" ]]; then
    echo "Missing shared environment file: ${shared_path}/.env" >&2
    exit 1
fi

mkdir -p "${releases_path}" "${shared_path}/storage"
mkdir -p \
    "${shared_path}/storage/app/public" \
    "${shared_path}/storage/framework/cache" \
    "${shared_path}/storage/framework/sessions" \
    "${shared_path}/storage/framework/views" \
    "${shared_path}/storage/logs"

mkdir "${release_path}"
tar -xzf "${ARCHIVE}" -C "${release_path}"
rm -f "${ARCHIVE}"

ln -s "${shared_path}/.env" "${release_path}/.env"
ln -s "${shared_path}/storage" "${release_path}/storage"

cd "${release_path}"

php artisan migrate --force
php artisan storage:link
php artisan optimize

ln -sfn "${release_path}" "${DEPLOY_PATH}/current.next"
mv -Tf "${DEPLOY_PATH}/current.next" "${DEPLOY_PATH}/current"

php artisan horizon:terminate || true
php artisan octane:reload || true

echo "Release ${RELEASE_ID} is active at ${DEPLOY_PATH}/current"
