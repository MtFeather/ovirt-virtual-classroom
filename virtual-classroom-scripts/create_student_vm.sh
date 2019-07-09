#!/bin/bash
source ./config.sh
source ./function.sh
temp_name="$1"
temp_disk_id="$2"
temp_image_id="$3"
temp_disk_size="$4"
storagedomain="$5"
vnic="$6"
student="$7"

echo "${temp_name}, ${temp_disk_id}, ${temp_image_id}, ${temp_disk_size}, ${storagedomain}, ${student}"

vm_xml=$( _create_vm "\
<vm>
  <name>${temp_name}_${student}</name>
  <cluster>
    <name>Default</name>
  </cluster>
  <template>
    <name>Blank</name>
  </template>
</vm>
")
#echo ${vm_xml}
vm_id=$(xmllint --xpath "/vm/@id" - <<< ${vm_xml} | sed 's/ id="\([^"]*\)"/\1/g' )
echo "vm_id=${vm_id}"

diskattachment_xml=$( _create_image " \
<disk_attachment>
  <bootable>true</bootable>
  <interface>virtio</interface>
  <active>true</active>
  <disk>
    <format>cow</format>
    <name>${temp_name}_${student}</name>
    <provisioned_size>${temp_disk_size}</provisioned_size>
    <storage_domains>
      <storage_domain id='${storagedomain}'/>
    </storage_domains>
  </disk>
</disk_attachment>
" \
"${vm_id}" )

disk_id=$( xmllint --xpath "/disk_attachment/@id" - <<< ${diskattachment_xml} | sed 's/ id="\([^"]*\)"/\1/g' )
echo "disk_id=${disk_id}"

disk_xml=$( _get_disk "${disk_id}" )
image_id=$( xmllint --xpath "/disk/image_id/text()" - <<< ${disk_xml} )

while true;
do
	#disk_status=$( xmllint --xpath "/disk/status/text()" - <<< ${disk_xml} )
        #echo ${disk_status}
        [ -f "${storage_path}/${storagedomain}/images/${disk_id}/${image_id}" ] && break;
done

echo ${image_id}

qemu-img create -f qcow2 -b ../${temp_disk_id}/${temp_image_id} ${storage_path}/${storagedomain}/images/${disk_id}/${image_id}

_create_vnic " \
<nic>
  <name>nic1</name>
  <vnic_profile id='${vnic}'/>
</nic>
" "${vm_id}" &> /dev/null
