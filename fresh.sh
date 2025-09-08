#!/bin/bash
set -e

echo "[*] Stopping all running containers..."
docker ps -q | xargs -r docker stop

echo "[*] Removing all containers with volumes..."
docker ps -aq | xargs -r docker rm -v

echo "[*] Removing all user-defined networks..."
docker network ls --quiet | while read netid; do
  name=$(docker network inspect -f '{{.Name}}' "$netid")
  if [[ "$name" != "bridge" && "$name" != "host" && "$name" != "none" ]]; then
    echo "    removing $name"
    docker network rm "$netid"
  fi
done

echo "[*] Removing dangling images..."
docker image prune -f

echo "[*] Done."