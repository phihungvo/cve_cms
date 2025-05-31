#!/bin/bash

# Kiểm tra Docker Compose v2
if ! command -v docker compose >/dev/null 2>&1; then
    echo "Error: Docker Compose v2 is required"
    exit 1
fi

# Xóa cache Laravel
if [ -d bootstrap/cache ]; then
    rm -rf bootstrap/cache/*.php
fi

# Sao chép .env nếu chưa tồn tại
if [ ! -f .env ] && [ -f docker/.env.example ]; then
    cp docker/.env.example .env
elif [ ! -f docker/.env.example ]; then
    echo "Error: docker/.env.example not found"
    exit 1
fi

# Sao chép docker-compose.yml nếu chưa tồn tại
if [ ! -f docker/docker-compose.yml ] && [ -f docker/docker-compose.yml.example ]; then
    cp docker/docker-compose.yml.example docker/docker-compose.yml
elif [ ! -f docker/docker-compose.yml.example ]; then
    echo "Error: docker/docker-compose.yml.example not found"
    exit 1
fi

# Dừng tất cả các container
docker compose -f docker/docker-compose.yml stop || { echo "Failed to stop containers"; exit 1; }

# Xóa tất cả container platform-worker
echo "Removing all platform-worker containers..."
docker ps -a --filter "name=platform-worker" -q | xargs -r docker rm -f

# Build và khởi động lại các service
docker compose -f docker/docker-compose.yml build || { echo "Failed to build images"; exit 1; }
docker compose -f docker/docker-compose.yml up -d --scale platform-worker=5 || { echo "Failed to start containers"; exit 1; }

echo "Build and deployment completed successfully!"