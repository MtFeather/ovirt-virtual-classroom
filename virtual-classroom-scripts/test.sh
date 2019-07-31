#!/bin/bash
source ./config.sh
check=$( mount | grep "${storage_path}" )
if [ "${check}" == "" ]; then
        [ ! -d ${storage_path} ] && mkdir ${storage_path}
        echo "wwww"
        mount -t nfs ${storage_nfs} ${storage_path}
fi
