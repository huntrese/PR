#!/bin/bash

set -e

mkdir -p /home/testuser
(
  echo "${FTP_USER_PASS}"
  echo "${FTP_USER_PASS}"
) | pure-pw useradd "${FTP_USER_NAME}" -u ftpuser -d /home/testuser -m
pure-pw mkdb
exec /usr/sbin/pure-ftpd $@
