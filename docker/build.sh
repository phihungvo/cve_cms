#!/bin/bash
set -e

echo "Docker Compose version:"
docker compose version

echo "Checking files..."
ls -l docker/docker-compose.yml docker/Dockerfile docker/.env.example docker/docker-compose.yml.example || { echo "Missing required files"; exit 1; }

echo "Checking build context..."
ls -l . | grep composer.json || { echo "Missing composer.json in context"; exit 1; }

if [ -d bootstrap/cache ]; then
    echo "Removing Laravel cache..."
    rm -rf bootstrap/cache/*.php
fi

if [ ! -f .env ] && [ -f docker/.env.example ]; then
    echo "Copying .env.example to .env..."
    cp docker/.env.example .env
elif [ ! -f docker/.env.example ]; then
    echo "Error: docker/.env.example not found"
    exit 1
fi

if [ ! -f docker/docker-compose.yml ] && [ -f docker/docker-compose.yml.example ]; then
    echo "Copying docker-compose.yml.example to docker-compose.yml..."
    cp docker/docker-compose.yml.example docker/docker-compose.yml
elif [ ! -f docker/docker-compose.yml.example ]; then
    echo "Error: docker/docker-compose.yml.example not found"
    exit 1
fi

echo "Validating docker-compose.yml..."
docker compose -f docker/docker-compose.yml config || { echo "Invalid docker-compose.yml"; exit 1; }

echo "Stopping all containers..."
docker compose -f docker/docker-compose.yml stop || { echo "Failed to stop containers"; exit 1; }

echo "Removing all platform-worker containers..."
docker ps -a --filter "name=platform-worker" -q | xargs -r docker rm -f

echo "Building platform-app..."
docker compose -f docker/docker-compose.yml build platform-app || { echo "Failed to build platform-app"; exit 1; }

echo "Building platform-worker..."
docker compose -f docker/docker-compose.yml build platform-worker || { echo "Failed to build platform-worker"; exit 1; }

echo "Starting containers..."
docker compose -f docker/docker-compose.yml up -d platform-app platform-worker platform-mysql platform-redis --scale platform-worker=5 || { echo "Failed to start containers"; exit 1; }

echo "Build and deployment completed successfully!"
echo "Application is running at http://localhost:8080"