#!/bin/sh
set -eu

export GITHUB="true"

# Debugging: Print TELEGRAM_TOKEN and TELEGRAM_CHAT_ID
echo "DEBUG: TELEGRAM_TOKEN=$TELEGRAM_TOKEN"
echo "DEBUG: TELEGRAM_CHAT_ID=$TELEGRAM_CHAT_ID"

[ -n "$*" ] && export TELEGRAM_MESSAGE="$*"

/bin/drone-telegram
