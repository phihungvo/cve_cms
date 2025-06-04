#!/bin/bash
set -e

# Kiểm tra Docker Compose v2
if ! command -v docker compose >/dev/null 2>&1; then
    echo "Error: Docker Compose v2 is required"
    exit 1
fi

# Kiểm tra version Docker Compose
echo "Docker Compose version:"
docker compose version

# Kiểm tra file tồn tại
echo "Checking files..."
ls -l docker/docker-compose.yml docker/Dockerfile docker/.env.example docker/docker-compose.yml.example || { echo "Missing required files"; exit 1; }

# Kiểm tra context
echo "Checking build context..."
ls -l .. | grep composer.json || { echo "Missing composer.json in context"; exit 1; }

# Xóa cache Laravel
if [ -d bootstrap/cache ]; then
    echo "Removing Laravel cache..."
    rm -rf bootstrap/cache/*.php
fi

# Sao chép .env nếu chưa tồn tại
if [ ! -f .env ] && [ -f docker/.env.example ]; then
    echo "Copying .env.example to .env..."
    cp docker/.env.example .env
elif [ ! -f docker/.env.example ]; then
    echo "Error: docker/.env.example not found"
    exit 1
fi

# Sao chép docker-compose.yml nếu chưa tồn tại
if [ ! -f docker/docker-compose.yml ] && [ -f docker/docker-compose.yml.example ]; then
    echo "Copying docker-compose.yml.example to docker-compose.yml..."
    cp docker/docker-compose.yml.example docker/docker-compose.yml
elif [ ! -f docker/docker-compose.yml.example ]; then
    echo "Error: docker/docker-compose.yml.example not found"
    exit 1
fi

# Kiểm tra cú pháp docker-compose.yml
echo "Validating docker-compose.yml..."
docker compose -f docker/docker-compose.yml config || { echo "Invalid docker-compose.yml"; exit 1; }

# Dừng tất cả các container
echo "Stopping all containers..."
docker compose -f docker/docker-compose.yml stop || { echo "Failed to stop containers"; exit 1; }

# Xóa tất cả container platform-worker
echo "Removing all platform-worker containers..."
docker ps -a --filter "name=platform-worker" -q | xargs -r docker rm -f

# Build các service
echo "Building platform-app..."
docker compose -f docker/docker-compose.yml build platform-app || { echo "Failed to build platform-app"; exit 1; }

echo "Building platform-worker..."
docker compose -f docker/docker-compose.yml build platform-worker || { echo "Failed to build platform-worker"; exit 1; }

# Khởi động các service
echo "Starting containers..."
docker compose -f docker/docker-compose.yml up -d platform-app platform-worker platform-mysql platform-redis --scale platform-worker=5 || { echo "Failed to start containers"; exit 1; }

echo "Build and deployment completed successfully!"
echo "Application is running at http://localhost:8080"