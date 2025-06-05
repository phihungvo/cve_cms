#!/bin/bash
set -e

echo "Docker Compose version:"
docker compose version

echo "Checking docker-compose.yml.example..."
if [ ! -f docker/docker-compose.yml.example ]; then
    echo "Error: docker/docker-compose.yml.example not found"
    exit 1
fi
ls -l docker/docker-compose.yml.example

echo "Copying docker-compose.yml if not exists..."
if [ ! -f docker/docker-compose.yml ]; then
    cp docker/docker-compose.yml.example docker/docker-compose.yml
    echo "Copied docker-compose.yml.example to docker-compose.yml"
fi

echo "Checking required files..."
ls -l docker/docker-compose.yml docker/Dockerfile docker/.env.example || { echo "Missing required files"; exit 1; }

echo "Checking build context..."
ls -l . | grep composer.json || { echo "Missing composer.json in context"; exit 1; }

# Xóa cache Laravel
rm -rf bootstrap/cache/*.php

if [ -f docker/.env.example ]; then
    echo "Copying docker/.env.example to .env..."
    cp docker/.env.example .env
else
    echo "Error: docker/.env.example not found"
    exit 1
fi


echo "Validating docker-compose.yml..."
docker compose -f docker/docker-compose.yml config || { echo "Invalid docker-compose.yml"; exit 1; }

echo "Stopping all containers..."
docker compose -f docker/docker-compose.yml stop || { echo "Failed to stop containers"; exit 1; }

echo "Removing all platform-worker containers..."
docker ps -a --filter "name=platform-worker" -q | xargs -r docker rm -f

# Build và khởi động lại các service
docker compose -f docker/docker-compose.yml build
docker compose -f docker/docker-compose.yml up -d --scale platform-worker=5

echo "Build and deployment completed successfully!"
echo "Application is running at http://localhost:8080"