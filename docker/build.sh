#!/bin/bash
set -e

# Kiểm tra Docker Compose v2
if ! command -v docker compose >/dev/null 2>&1; then
    echo "Error: Docker Compose v2 is required"
    exit 1
fi

echo "Docker Compose version:"
docker compose version

# Xóa cache Laravel
if [ -d bootstrap/cache ]; then
    rm -rf bootstrap/cache/*.php
fi

# Sao chép .env nếu chưa tồn tại
if [ ! -f .env ] && [ -f docker/.env.example ]; then
    cp docker/.env.example .env
    echo "Copied docker/.env.example to .env"
elif [ ! -f docker/.env.example ]; then
    echo "Error: docker/.env.example not found"
    exit 1
fi

# Kiểm tra docker-compose.yml.example
if [ ! -f docker/docker-compose.yml.example ]; then
    echo "Error: docker/docker-compose.yml.example not found"
    exit 1
fi
ls -l docker/docker-compose.yml.example

# Sao chép docker-compose.yml nếu chưa tồn tại
if [ ! -f docker/docker-compose.yml ] && [ -f docker/docker-compose.yml.example ]; then
    cp docker/docker-compose.yml.example docker/docker-compose.yml
    echo "Copied docker-compose.yml.example to docker-compose.yml"
elif [ ! -f docker/docker-compose.yml.example ]; then
    echo "Error: docker/docker-compose.yml.example not found"
    exit 1
fi

# Kiểm tra các file cần thiết
echo "Checking required files..."
ls -l docker/docker-compose.yml docker/Dockerfile docker/.env.example || { echo "Missing required files"; exit 1; }

# Kiểm tra build context
echo "Checking build context..."
ls -l . | grep composer.json || { echo "Missing composer.json in context"; exit 1; }

# Xác thực docker-compose.yml
echo "Validating docker-compose.yml..."
docker compose -f docker/docker-compose.yml config || { echo "Invalid docker-compose.yml"; exit 1; }

# Dừng tất cả các container
echo "Stopping all containers..."
docker compose -f docker/docker-compose.yml stop || { echo "Failed to stop containers"; exit 1; }

# Xóa container platform-worker
echo "Removing all platform-worker containers..."
docker ps -a --filter "name=platform-worker" -q | xargs -r docker rm -f

# Build và khởi động lại các service
echo "Building and starting containers..."
docker compose -f docker/docker-compose.yml build || { echo "Failed to build images"; exit 1; }
docker compose -f docker/docker-compose.yml up -d --scale platform-worker=5 || { echo "Failed to start containers"; exit 1; }

echo "Build and deployment completed successfully!"
echo "Application is running at http://localhost:8080"