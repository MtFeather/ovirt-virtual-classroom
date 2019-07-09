var VIRTUAL_CLASS_PLUGIN_MESSAGE_PREFIX = 'virtual-class-plugin';
var VIRTUAL_CLASS_PLUGIN_MESSAGE_DELIM = ':';

function getUsers(users){
  users.forEach(function(user) {
    $('#newTeacher').append('<option value="' + user + '">' + user + '</option>');
  });
}

function editUsers(users){
  users.forEach(function(user) {
    $('#editTeacher').append('<option value="' + user + '">' + user + '</option>');
  });
}

function getCurriculumsList(){
  var curriculums_Table = $("#curriculums_table").DataTable({
    "ajax": {
      url: "function.php?f=getCurriculumsList",
      type: "GET"
    },
    "columns": [
       { "data": "id" },
       { 
         "data": "name",
         "render": function ( data, type, row, meta ) {
           return '<a href="curriculum_student.php?id='+row.id+'&name='+data+'">'+data+'</a>';
         }
       },
       { "data": "teacher" },
       { 
         "data": null,
         "render": function ( data, type, row, meta ) {
           return row.num+"/"+row.maxnum;
         }
       }
    ],
    "dom": "<'content-view-pf-pagination clearfix'"+
           "<'form-group'B>"+
           "<'form-group'<i><'btn-group btn-pagination'p>>>t",
    "pagingType": "simple",
    "pageLength": 100,
    "language": {
      "zeroRecords": "No matching records found",
      "info": "_START_ - _END_",
      "paginate": {
        "previous": '<i class="fa fa-angle-left"></i>',
        "next": '<i class="fa fa-angle-right"></i>'
      },
      "select": {
        rows: ""
      }
    },
    "order": [[ 1, "asc" ]],
    rowId: 'name',
    select: {
      items: 'row'
    },
    buttons: [
      {
        "text": '<i class="fa fa-refresh"></i>',
        "className": 'btn btn-default',
        "action": function ( e, dt, node, config ) {
          dt.ajax.reload();
        }
      }
    ],
    "columnDefs": [
      {
          "targets": [ 0 ],
          "visible": false,
          "searchable": false
      }
    ]
  });

  $('#SearchPanelView_searchStringInput').keyup(function(){
    curriculums_Table.search($(this).val()).draw();
  });

  $('#SearchPanelView_searchClean').click(function(){
    $('#SearchPanelView_searchStringInput').val('');
    curriculums_Table.search('').draw();
  });

  curriculums_Table.on( 'select.dt deselect.dt', function (){
    var rows = curriculums_Table.rows( { selected: true } ).indexes().length;
    if(rows === 0){
      $('#editBtn').attr('disabled', true);
      $('#deleteBtn').attr('disabled', true);
      $('#addBtn').attr('disabled', true);
      $('#removeBtn').attr('disabled', true);
    } else if(rows === 1){
      $('#editBtn').attr('disabled', false);
      $('#deleteBtn').attr('disabled', false);
      $('#addBtn').attr('disabled', false);
      $('#removeBtn').attr('disabled', false);
    } else {
      $('#editBtn').attr('disabled', true);
      $('#deleteBtn').attr('disabled', false);
      $('#addBtn').attr('disabled', true);
      $('#removeBtn').attr('disabled', true);
    }
  });
}

function createCurriculum() {
  $.ajax({
    url: "function.php?f=createCurriculum",
    method: "POST",
    data: { name: $('#newCurriculum_name').val(), teacher: $('#newTeacher').val(), maxnum: $('#newMaxnum').val() },
    success: function(result) {
      if (result == 'ok') {
        alert('Create Curriculum Successfully.');
        $('#newModal').modal('hide');
        $('#curriculums_table').DataTable().ajax.reload();
      } else {
        alert('Create Fail!!');
      }
    }
  });
}

function editCurriculum(id) {
  $.ajax({
    url: "function.php?f=editCurriculum",
    method: "POST",
    data: { id: id, name: $('#editCurriculum_name').val(), teacher: $('#editTeacher').val(), maxnum: $('#editMaxnum').val() },
    success: function(result) {
      if (result == 'ok') {
        alert('Edit Curriculum Successfully.');
        $('#editModal').modal('hide');
      } else {
        alert('Edit Fail!!');
      }
    }
  });
}


function deleteCurriculum(id) {
  $.ajax({
    url: "function.php?f=deleteCurriculum",
    method: "POST",
    data: { id: id },
    success: function(result) {
      if (result == 'ok') {
        $('#deleteModal').modal('hide');
      } else {
        alert('Delete Fail!!');
      }
    }
  });
}

function addCurriculumList(classId, searchId) {
   var table = $("#add_table").DataTable({
    "ajax": {
      url: "function.php?f=addCurriculumList",
      type: "POST",
      data: function(d){ 
        d.classId = classId;
        d.searchId = searchId;
      }
    },
    "columns": [
       { 
         "data": "id",
         "render": function ( data, type, row, meta ) {
           return '<input type="checkbox" value="'+data+'">';
         }
       },
       { "data": "account" },
       { "data": "name" },
    ],
    "dom": "<'content-view-pf-pagination clearfix'"+
           "<'form-group'B>"+
           "<'form-group'<i><'btn-group btn-pagination'p>>>t",
    "pagingType": "simple_numbers",
    "pageLength": 10,
    "language": {
      "zeroRecords": "No matching records found",
      "info": "_START_ - _END_",
      "paginate": {
        "previous": '<i class="fa fa-angle-left"></i>',
        "next": '<i class="fa fa-angle-right"></i>'
      },
      "select": {
        rows: ""
      }
    },
    "order": [[ 1, "asc" ]],
    "columnDefs": [
      {
        targets: 0,
        orderable: false
      }
    ],
    select: {
      items: 'row',
      style: 'multi',
      selector: 'td:first-child input:checkbox'
    },
    buttons: [
      {
        "text": '<i class="fa fa-refresh"></i>',
        "className": 'btn btn-default',
        "action": function ( e, dt, node, config ) {
          dt.ajax.reload();
        }
      }
    ]
  });
 
  $('#addTable_search').keyup(function(){
    table.search($(this).val()).draw();
  });

  $('#addTable_searchClean').click(function(){
    $('#addTable_search').val('');
    table.search('').draw();
  });
}

function addCurriculum(classId, students) {
  $.ajax({
    url: "function.php?f=addCurriculum",
    method: "POST",
    data: { classId: classId, students: students },
    success: function(result) {
      if (result == 'ok') {
        alert('Add Student Successfully.');
        $('#addModal').modal('hide');
      } else {
        alert('Add Student Fail!!');
      }
    }
  });
}

function removeCurriculumList(classId) {
   var table = $("#remove_table").DataTable({
    "ajax": {
      url: "function.php?f=getCurriculumStudent",
      type: "POST",
      data: { classId: classId }
    },
    "columns": [
       { 
         "data": "id",
         "render": function ( data, type, row, meta ) {
           return '<input type="checkbox" value="'+data+'">';
         }
       },
       { "data": "account" },
       { "data": "name" },
    ],
    "dom": "<'content-view-pf-pagination clearfix'"+
           "<'form-group'B>"+
           "<'form-group'<i><'btn-group btn-pagination'p>>>t",
    "pagingType": "simple_numbers",
    "pageLength": 10,
    "language": {
      "zeroRecords": "No matching records found",
      "info": "_START_ - _END_",
      "paginate": {
        "previous": '<i class="fa fa-angle-left"></i>',
        "next": '<i class="fa fa-angle-right"></i>'
      },
      "select": {
        rows: ""
      }
    },
    "order": [[ 1, "asc" ]],
    "columnDefs": [
      {
        targets: 0,
        orderable: false
      }
    ],
    select: {
      items: 'row',
      style: 'multi',
      selector: 'td:first-child input:checkbox'
    },
    buttons: [
      {
        "text": '<i class="fa fa-refresh"></i>',
        "className": 'btn btn-default',
        "action": function ( e, dt, node, config ) {
          dt.ajax.reload();
        }
      }
    ]
  });
 
  $('#removeTable_search').keyup(function(){
    table.search($(this).val()).draw();
  });

  $('#removeTable_searchClean').click(function(){
    $('#removeTable_search').val('');
    table.search('').draw();
  });
}

function removeCurriculum(classId, students) {
  $.ajax({
    url: "function.php?f=removeCurriculum",
    method: "POST",
    data: { classId: classId, students: students },
    success: function(result) {
      if (result == 'ok') {
        alert('Remove Student Successfully.');
        $('#removeModal').modal('hide');
      } else {
        alert('Remove Student Fail!!');
      }
    }
  });
}

function getCurriculumStudent(classId) {
   var table = $("#students_table").DataTable({
    "ajax": {
      url: "function.php?f=getCurriculumStudent",
      type: "POST",
      data: { classId: classId }
    },
    "columns": [
       { 
         "data": "id",
         "render": function ( data, type, row, meta ) {
           return '<input type="checkbox" value="'+data+'">';
         }
       },
       { "data": "account" },
       { "data": "name" },
    ],
    "dom": "<'content-view-pf-pagination clearfix'"+
           "<'form-group'B>"+
           "<'form-group'<i><'btn-group btn-pagination'p>>>t",
    "pagingType": "simple_numbers",
    "pageLength": 100,
    "language": {
      "zeroRecords": "No matching records found",
      "info": "_START_ - _END_",
      "paginate": {
        "previous": '<i class="fa fa-angle-left"></i>',
        "next": '<i class="fa fa-angle-right"></i>'
      },
      "select": {
        rows: ""
      }
    },
    "order": [[ 1, "asc" ]],
    "columnDefs": [
      {
        targets: 0,
        orderable: false
      }
    ],
    select: {
      items: 'row',
      style: 'multi',
      selector: 'td:first-child input:checkbox'
    },
    buttons: [
      {
        "text": '<i class="fa fa-refresh"></i>',
        "className": 'btn btn-default',
        "action": function ( e, dt, node, config ) {
          dt.ajax.reload();
        }
      }
    ]
  });
 
  $('#SearchPanelView_searchStringInput').keyup(function(){
    table.search($(this).val()).draw();
  });

  $('#SearchPanelView_searchClean').click(function(){
    $('#SearchPanelView_searchStringInput').val('');
    table.search('').draw();
  });

  table.on( 'select.dt deselect.dt', function (){
    var rows = table.rows( { selected: true } ).indexes().length;
    if(rows === 0){
      $('#removeBtn').attr('disabled', true);
    } else {
      $('#removeBtn').attr('disabled', false);
    }
  });
}

function validateNewForm() {
  var e = '';
  var c = ['newCurriculum_name', 'newTeacher', 'newMaxnum'];
  for (var i=0;i<c.length;i++) {
    if ($('#'+c[i]).val() == '') {
      e = 'false';
      $('#'+c[i]).parents('.form-group').addClass('has-error');
    } else {
      $('#'+c[i]).parents('.form-group').removeClass('has-error');
    }
  }
  if (isNaN($('#newMaxnum').val())) {
    e = 'false';
    $('#newMaxnum').parents('.form-group').addClass('has-error');
  }
  return e;
}

function validateEditForm() {
  var e = '';
  var c = ['editCurriculum_name', 'editTeacher', 'editMaxnum'];
  for (var i=0;i<c.length;i++) {
    if ($('#'+c[i]).val() == '') {
      e = 'false';
      $('#'+c[i]).parents('.form-group').addClass('has-error');
    } else {
      $('#'+c[i]).parents('.form-group').removeClass('has-error');
    }
  }
  if (isNaN($('#editMaxnum').val())) {
    e = 'false';
    $('#editMaxnum').parents('.form-group').addClass('has-error');
  }
  return e;
}

function getTemplatesList(templates) {
  var table = $("#templates_table").DataTable({
    "data": templates,
    "columns": [
       { "data": "t_name" },
       { "data": "d_name" },
       { "data": "d_id" },
       { 
         "data": "d_size",
         "render": function ( data, type, row, meta ) {
           return data/1024/1024/1024+'G';
         }
       },
       { "data": "i_id" },
    ],
    "dom": "<'content-view-pf-pagination clearfix'"+
           "<'form-group'>"+
           "<'form-group'<i><'btn-group btn-pagination'p>>>t",
    "pagingType": "simple",
    "pageLength": 100,
    "language": {
      "zeroRecords": "No matching records found",
      "info": "_START_ - _END_",
      "paginate": {
        "previous": '<i class="fa fa-angle-left"></i>',
        "next": '<i class="fa fa-angle-right"></i>'
      },
      "select": {
        rows: ""
      }
    },
    rowId: 't_id',
    select: {
      items: 'row'
    }
  });

  $('#SearchPanelView_searchStringInput').keyup(function(){
    table.search($(this).val()).draw();
  });

  $('#SearchPanelView_searchClean').click(function(){
    $('#SearchPanelView_searchStringInput').val('');
    table.search('').draw();
  });

  table.on( 'select.dt deselect.dt', function (){
    var rows = table.rows( { selected: true } ).indexes().length;
    if(rows === 0){
      $('#editBtn').attr('disabled', true);
      $('#deleteBtn').attr('disabled', true);
      $('#addBtn').attr('disabled', true);
      $('#removeBtn').attr('disabled', true);
    } else if(rows === 1){
      $('#editBtn').attr('disabled', false);
      $('#deleteBtn').attr('disabled', false);
      $('#addBtn').attr('disabled', false);
      $('#removeBtn').attr('disabled', false);
    } else {
      $('#editBtn').attr('disabled', true);
      $('#deleteBtn').attr('disabled', false);
      $('#addBtn').attr('disabled', true);
      $('#removeBtn').attr('disabled', true);
    }
  });
}

function getStorageDomainsList(storagedomains) {
  for (var i=0; i<storagedomains.length; i++) {
    $('#storage_select').append('<option value="'+storagedomains[i].id+'">'+storagedomains[i].name+' ('+storagedomains[i].available/1024/1024/1024+' GiB free)</option>');
  }
}

function getVNicProfilesList(vnics) {
  for (var i=0; i<vnics.length; i++) {
    $('#vnic_select').append('<option value="'+vnics[i].id+'">'+vnics[i].name+'</option>');
  }
}

function getCurriculumName() {
  $.ajax({
    url: "function.php?f=getCurriculumName",
    method: "GET",
    success: function(result) {
      var data = JSON.parse(result);
      $('#curriculum').append('<option value="null">Choose a curriculum</option>');
      for (var i=0; i<data.length; i++) {
        $('#curriculum').append('<option value="'+data[i].id+'">'+data[i].name+'</option>');
      }
    }
  });  
}

function addImageList(t_id, c_select) {
   var table = $("#add_table").DataTable({
    "ajax": {
      url: "function.php?f=addImageList",
      type: "POST",
      data: function(d){ 
        d.tempId = t_id;
        d.classId = c_select;
      }
    },
    "columns": [
       { 
         "data": "id",
         "render": function ( data, type, row, meta ) {
           return '<input type="checkbox" value="'+data+'">';
         }
       },
       { "data": "account" },
       { "data": "name" },
    ],
    "dom": "<'content-view-pf-pagination clearfix'"+
           "<'form-group'B>"+
           "<'form-group'<i><'btn-group btn-pagination'p>>>t",
    "pagingType": "simple_numbers",
    "pageLength": 10,
    "language": {
      "zeroRecords": "No matching records found",
      "info": "_START_ - _END_",
      "paginate": {
        "previous": '<i class="fa fa-angle-left"></i>',
        "next": '<i class="fa fa-angle-right"></i>'
      },
      "select": {
        rows: ""
      }
    },
    "order": [[ 1, "asc" ]],
    "columnDefs": [
      {
        targets: 0,
        orderable: false
      }
    ],
    select: {
      items: 'row',
      style: 'multi',
      selector: 'td:first-child input:checkbox'
    },
    buttons: [
      {
        "text": '<i class="fa fa-refresh"></i>',
        "className": 'btn btn-default',
        "action": function ( e, dt, node, config ) {
          dt.ajax.reload();
        }
      }
    ]
  });
 
  $('#addTable_search').keyup(function(){
    table.search($(this).val()).draw();
  });

  $('#addTable_searchClean').click(function(){
    $('#addTable_search').val('');
    table.search('').draw();
  });
}

function createStudentImage(t_id, t_name, d_id, i_id, d_size, storagedomain, vnic, students){
  $.ajax({
    url: "function.php?f=createStudentImage",
    method: "POST",
    data: { tempId: t_id, tempName: t_name, diskId: d_id, imageId: i_id, diskSize: d_size, storagedomain: storagedomain, vnic: vnic, students: students },
    success: function(result) {
     console.log(result);
     // if (result == 'ok') {
     //   alert('Remove Student Successfully.');
     //   $('#removeModal').modal('hide');
     // } else {
     //   alert('Remove Student Fail!!');
     // }
    }
  });
}
