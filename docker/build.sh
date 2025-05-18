#!/bin/bash

# Xóa cache Laravel
rm -rf bootstrap/cache/*.php

# Sao chép .env nếu chưa tồn tại
if [ ! -f .env ]; then
    cp docker/.env.example .env
fi

# Sao chép docker-compose.yml nếu chưa tồn tại
if [ ! -f docker/docker-compose.yml ]; then
    cp docker/docker-compose.yml.example docker/docker-compose.yml
fi

# Dừng tất cả các container
docker compose -f docker/docker-compose.yml stop

# Xóa tất cả container platform-worker
echo "Removing all platform-worker containers..."
docker ps -a --filter "name=platform-worker" -q | xargs -r docker rm -f

# Build và khởi động lại các service
docker compose -f docker/docker-compose.yml build
docker compose -f docker/docker-compose.yml up -d --scale platform-worker=5