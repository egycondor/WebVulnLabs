#!/usr/bin/env bash
set -euo pipefail

# --- CONFIG ---
OWNER_REPO="${OWNER_REPO:-egycondor/WebVulnLabs}"
BRANCH="${BRANCH:-main}"
PROJECT_DIR="${PROJECT_DIR:-/home/ubuntu/vuln-labs}"
REDEPLOY="${REDEPLOY:-false}"

RAW_VER_URL="https://raw.githubusercontent.com/${OWNER_REPO}/${BRANCH}/VERSION"
TARBALL_URL="https://codeload.github.com/${OWNER_REPO}/tar.gz/refs/heads/${BRANCH}"

log(){ printf "[%s] %s\n" "$(date +'%F %T')" "$*"; }
local_ver(){ [ -f "${PROJECT_DIR}/VERSION" ] && cat "${PROJECT_DIR}/VERSION" || echo "0.0.0"; }
remote_ver(){ curl -fsSL "$RAW_VER_URL"; }

redeploy(){
  [ "${REDEPLOY}" = "true" ] || { log "Redeploy disabled."; return; }
  for lab in lab01 lab02; do
    if [ -f "${PROJECT_DIR}/${lab}/docker-compose.yml" ]; then
      log "Rebuilding ${lab}…"
      (cd "${PROJECT_DIR}/${lab}" && docker compose up -d --build)
    fi
  done
}

main(){
  mkdir -p "${PROJECT_DIR}"
  LV="$(local_ver)"
  RV="$(remote_ver || true)"

  if [ -z "${RV:-}" ]; then
    log "Couldn’t read remote VERSION. Abort."
    exit 1
  fi

  if [ "$LV" = "$RV" ]; then
    log "Up to date (VERSION ${LV})."
    exit 0
  fi

  log "Update found: ${LV} -> ${RV}. Downloading…"
  tmp="$(mktemp -d)"; trap 'rm -rf "$tmp"' EXIT
  curl -fsSL "$TARBALL_URL" -o "${tmp}/repo.tar.gz"
  tar -xzf "${tmp}/repo.tar.gz" -C "$tmp"

  src_dir="$(find "$tmp" -maxdepth 1 -type d -name "*-${BRANCH}" | head -n1)"
  [ -d "$src_dir" ] || { log "Extracted folder not found."; exit 1; }

  log "Syncing files into ${PROJECT_DIR}…"
  rsync -a --delete \
    --exclude ".env" \
    --exclude ".env.*" \
    --exclude "*/data/" \
    --exclude "*/volumes/" \
    --exclude "*.log" \
    "${src_dir}/" "${PROJECT_DIR}/"

  log "Updated to VERSION ${RV}."
  chmod +x "${PROJECT_DIR}/update.sh" || true
#  redeploy
}

main "$@"
