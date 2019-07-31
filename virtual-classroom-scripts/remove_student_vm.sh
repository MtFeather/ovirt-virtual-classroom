#!/bin/bash
source ./config.sh
source ./function.sh

wait $!

vm_id="$1"
_remove_vm "${vm_id}"

_psql "DELETE FROM student_vms WHERE vm_id = '${vm_id}';" &> /dev/null
