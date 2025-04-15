#!/bin/bash

if [ "$1" != "" ]; then
    name="$1"
else
    name="app"
fi

container=$(sudo docker ps | grep "platform-$name" | awk -F' ' '{print $1}')

if [ "$container" == "" ]; then
    echo ""
    echo "Container platform-$name is not available yet"
    echo ""

    exit 1
fi

sudo docker logs --details "$container"
