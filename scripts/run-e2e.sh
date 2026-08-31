#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

MODE="${1:-headless}"

case "$MODE" in
  headless)
    npx playwright test --project=chromium --reporter=list
    ;;
  headed)
    npx playwright test --project=chromium --headed --reporter=list
    ;;
  ui)
    npx playwright test --project=chromium --ui
    ;;
  debug)
    npx playwright test --project=chromium --debug
    ;;
  *)
    echo "Usage: $0 [headless|headed|ui|debug]"
    exit 1
    ;;
esac
