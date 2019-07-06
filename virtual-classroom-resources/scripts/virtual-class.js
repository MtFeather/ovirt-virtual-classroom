var VIRTUAL_CLASS_PLUGIN_MESSAGE_PREFIX = 'virtual-class-plugin';
var VIRTUAL_CLASS_PLUGIN_MESSAGE_DELIM = ':';

function getUsers(users){
      users.forEach(function(user) {
        $('#teacher').append('<option value="' + user + '">' + user + '</option>');
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
       { "data": "name" },
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
      $('#addBtn').attr('disabled', true);
      $('#deleteBtn').attr('disabled', true);
    } else if(rows === 1){
      $('#addBtn').attr('disabled', false);
      $('#deleteBtn').attr('disabled', false);
    } else {
      $('#addBtn').attr('disabled', true);
      $('#deleteBtn').attr('disabled', false);
    }
  });
}

function createCurriculum() {
  $.ajax({
    url: "function.php?f=createCurriculum",
    method: "POST",
    data: { name: $('#curriculum_name').val(), teacher: $('#teacher').val(), maxnum: $('#maxnum').val() },
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

function deleteCurriculum() {
  var table = $("#curriculums_table").DataTable();
  var rows = table.rows( { selected: true } ).indexes();
  var id = table.rows(rows).data().pluck('id').toArray();
  $.ajax({
    url: "function.php?f=deleteCurriculum",
    method: "POST",
    data: { id: id },
    success: function(result) {
      table.ajax.reload();
      table.rows('.selected').deselect();
      $('#deleteModal').modal('hide');
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

function addCurriculum() {
  var table = $("#curriculums_table").DataTable();
  var rows = table.rows( { selected: true } ).indexes();
  var classId = table.rows(rows).data().pluck('id').toArray()[0];
  var maxnum = table.rows(rows).data().pluck('maxnum').toArray()[0]
  var num = table.rows(rows).data().pluck('num').toArray()[0]
  var students = $('#add_table td input:checkbox:checked').map(function(){
        return $(this).val();
      }).get();
  if (students.length == 0) {
    alert('Please choose student!');
  } else if (students.length + num > maxnum) {
    alert('More than the maximum number');
  } else {
    $.ajax({
      url: "function.php?f=addCurriculum",
      method: "POST",
      data: { classId: classId, students: students },
      success: function(result) {
        if (result == 'ok') {
          alert('Add Student Successfully.');
          $('#addModal').modal('hide');
          table.ajax.reload();
        } else {
          alert('Add Student Fail!!');
        }
      }
    });
  }
}

function getPodsList(){
  var namespace = $('#namespaces').val();
  var pods_Table = $("#pods_table").DataTable({
    "ajax": {
      url: "function.php?f=getPodsList",
      type: "POST",
      data: function(d){ 
        d.namespace = $('#namespaces').val(); 
      }
    },
    "columns": [
       {
         "data": "status",
         "render": function ( data, type, row, meta ) {
           if (data == "Running") {
             return '<span class="glyphicon glyphicon-triangle-top" style="color: #00f452; font-size: 11pt;"></span>';
           } else if (data == "Pending") {
             return '<span class="pficon pficon-builder-image" style="color: #ff2c2a; font-size: 11pt;"></span>';
           }
         }
       },
       {
         "data": "name",
         "render": function ( data, type, row, meta ) {
           return '<a href="pod.php?namespace='+row.namespace+'&name='+data+'">'+data+'</a>';
         }
       },
       { "data": "namespace" },
       { "data": "host_ip" },
       { "data": "pod_ip" },
       { "data": "restarts" },
       { "data": "status" },
       { "data": "uptime" }
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
    "columnDefs": [{
      "targets": 0,
      "orderable": false 
    }],
    rowId: 'pod_ip',
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
    ]
  });

  $('#SearchPanelView_searchStringInput').keyup(function(){
    pods_Table.search($(this).val()).draw();
  });

  $('#SearchPanelView_searchClean').click(function(){
    $('#SearchPanelView_searchStringInput').val('');
    pods_Table.search('').draw();
  });

  pods_Table.on( 'select.dt deselect.dt', function (){
    var rows = pods_Table.rows( { selected: true } ).indexes().length;
    if(rows === 0){
      $('#editBtn').attr('disabled', true);
      $('#deleteBtn').attr('disabled', true);
      $('#consoleBtn').attr('disabled', true);
    } else if(rows === 1){
      $('#editBtn').attr('disabled', false);
      $('#deleteBtn').attr('disabled', false);
      $('#consoleBtn').attr('disabled', false);
    } else {
      $('#editBtn').attr('disabled', true);
      $('#deleteBtn').attr('disabled', false);
      $('#consoleBtn').attr('disabled', true);
    }
  });
}

function createPod() {
  $.ajax({
    url: "function.php?f=createPod",
    method: "POST",
    data: { namespace: $('#namespaces').val(), comment: $('#newComment').val() },
    success: function(result) {
      if (result == 'ok') {
        alert('Create Pod Successfully.');
        $('#newAlert').hide('fade');
        $('#newModal').modal('hide');
        $('#newComment').val('');
        $('#pods_table').DataTable().ajax.reload();
      } else {
        $('#newAlert div').html(result);
        $('#newAlert').show('fade');
      }
    }
  });
}

function getPodYaml(pod, namespace) {
  $.ajax({
    url: "function.php?f=getPodYaml",
    method: "POST",
    data: { pod: pod, namespace: namespace },
    success: function(result) {
      var obj = JSON.parse(result);
      var json = JSON.stringify(obj, undefined, 2);
      $('#editComment').val(json);
    }
  });
}

function editPod(pod, namespace) {
  $.ajax({
    url: "function.php?f=editPod",
    method: "POST",
    data: { pod: pod, namespace: namespace, data: $('#editComment').val() },
    success: function(result) {
      if (result == 'ok') {
        alert('Edit Pod Successfully.');
        $('#editModal').modal('hide');
        $('#pods_table').DataTable().ajax.reload();
      } else {
        alert(result);
        $('#newAlert').show('fade');
      }
    }
  });
}

function deletePod(pods, namespaces) {
  $.ajax({
    url: "function.php?f=deletePod",
    method: "POST",
    data: { pods: pods, namespaces: namespaces },
    success: function(result) {
      alert('Has been released to delete the pod(s).\nPlease reflash table to check.');
      $('#pods_table').DataTable().ajax.reload();
      $('#pods_table').DataTable().rows('.selected').deselect();
      $('#deleteModal').modal('hide');
    }
  });
}

function validateNewForm() {
  var e = '';
  var c = ['curriculum_name', 'teacher', 'maxnum'];
  for (var i=0;i<c.length;i++) {
    if ($('#'+c[i]).val() == '') {
      e = 'false';
      $('#'+c[i]).parents('.form-group').addClass('has-error');
    } else {
      $('#'+c[i]).parents('.form-group').removeClass('has-error');
    }
  }
  if (isNaN($('#maxnum').val())) {
    e = 'false';
    $('#maxnum').parents('.form-group').addClass('has-error');
  }
  return e;
}
