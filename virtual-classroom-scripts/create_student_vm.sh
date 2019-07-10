#!/bin/bash
source ./config.sh
source ./function.sh
temp_id="$1"
temp_name="$2"
temp_disk_id="$3"
temp_image_id="$4"
temp_disk_size="$5"
storagedomain="$6"
vnic="$7"
student_id="$8"
student_name="$9"

vm_name="${temp_name}_${student_name}"
vm_xml=$( _create_vm "\
<vm>
  <name>${vm_name}</name>
  <cluster>
    <name>Default</name>
  </cluster>
  <template>
    <name>Blank</name>
  </template>
</vm>
")
vm_id=$(xmllint --xpath "/vm/@id" - <<< ${vm_xml} | sed 's/ id="\([^"]*\)"/\1/g' )

diskattachment_xml=$( _create_image " \
<disk_attachment>
  <bootable>true</bootable>
  <interface>virtio</interface>
  <active>true</active>
  <disk>
    <format>cow</format>
    <name>${vm_name}</name>
    <provisioned_size>${temp_disk_size}</provisioned_size>
    <storage_domains>
      <storage_domain id='${storagedomain}'/>
    </storage_domains>
  </disk>
</disk_attachment>
" \
"${vm_id}" )

disk_id=$( xmllint --xpath "/disk_attachment/@id" - <<< ${diskattachment_xml} | sed 's/ id="\([^"]*\)"/\1/g' )

disk_xml=$( _get_disk "${disk_id}" )
image_id=$( xmllint --xpath "/disk/image_id/text()" - <<< ${disk_xml} )

check=$( mount | grep "${storage_path}" )
if [ "${check}" == "" ]; then
	[ ! -d ${storage_path} ] && mkdir ${storage_path}
	mount -t nfs ${storage_nfs} ${storage_path}
fi

while true;
do
        [ -f "${storage_path}/ovirt_data/${storagedomain}/images/${disk_id}/${image_id}" ] && break
done

qemu-img create -f qcow2 -b ../${temp_disk_id}/${temp_image_id} ${storage_path}/ovirt_data/${storagedomain}/images/${disk_id}/${image_id}

_create_vnic " \
<nic>
  <name>nic1</name>
  <vnic_profile id='${vnic}'/>
</nic>
" "${vm_id}" &> /dev/null

_psql "INSERT INTO student_vms (student, vm_name, vm_id, template_id, disk_id, image_id, create_time) VALUES (${student_id}, '${vm_name}', '${vm_id}', '${temp_id}', '${disk_id}', '${image_id}', now());" &> /dev/null
