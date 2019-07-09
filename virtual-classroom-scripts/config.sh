#!/bin/bash
# Directory Path
resources_dir="/dev/shm/ovirt-cache/"
session_file="${resources_dir}/session.txt"
storage_path="/gocloud_fs/ovirt_data"

# Command Path
psql="/opt/rh/rh-postgresql10/root/usr/bin/psql -U gocloud"

# Ovirt REST API INFO
url="https://localhost/ovirt-engine/api"
user="admin@internal"
password="password"

[ ! -d ${basedir} ] && mkdir -p ${basedir}
