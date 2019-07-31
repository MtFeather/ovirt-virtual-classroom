#!/bin/bash
source ./config.sh
source ./function.sh

temp_id="$1"
temp_name="$2"
vnic=$3
student_id="$4"
student_name="$5"

vm_name="${temp_name}_${student_name}"
vm_xml=$( _create_vm "\
<vm>
  <name>${vm_name}</name>
  <cluster>
    <name>Default</name>
  </cluster>
  <template>
    <name>${temp_name}</name>
  </template>
</vm>
" )
vm_id=$( xmllint --xpath "/vm/@id" - <<< ${vm_xml} | sed 's/ id="\([^"]*\)"/\1/g' )

vm_disk_xml=$( _vm_disk_xml ${vm_id} )
disk_id=$( xmllint --xpath "//disk_attachment[1]/disk/@id" - <<< ${vm_disk_xml} | sed 's/ id="\([^"]*\)"/\1/g' )
image_id=$(xmllint --xpath "//disk_attachment[1]/disk/image_id/text()" - <<< ${vm_disk_xml} )

_create_vnic " \
<nic>
  <name>nic1</name>
  <vnic_profile id='${vnic}'/>
</nic>
" "${vm_id}" &> /dev/null

_psql "INSERT INTO student_vms (student, vm_name, vm_id, template_name, template_id, disk_id, image_id, create_time) VALUES (${student_id}, '${vm_name}', '${vm_id}', '${temp_name}', '${temp_id}', '${disk_id}', '${image_id}', now());" &> /dev/null
