<?php
  $classId = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="/ovirt-engine/webadmin/theme/00-ovirt.brand/bundled/patternfly/css/patternfly.min.css">
    <link rel="stylesheet" type="text/css" href="/ovirt-engine/webadmin/theme/00-ovirt.brand/bundled/patternfly/css/patternfly-additions.min.css">
    <link rel="stylesheet" type="text/css" href="/ovirt-engine/webadmin/theme/00-ovirt.brand/common.css">
    <link rel="stylesheet" type="text/css" href="/ovirt-engine/webadmin/theme/00-ovirt.brand/webadmin.css">
    <script type="text/javascript" src="/ovirt-engine/webadmin/theme/00-ovirt.brand/bundled/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/ovirt-engine/webadmin/theme/00-ovirt.brand/bundled/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/ovirt-engine/webadmin/theme/00-ovirt.brand/bundled/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="./scripts/datatables.min.js"></script>
    <script type="text/javascript" src="/ovirt-engine/webadmin/theme/00-ovirt.brand/bundled/patternfly/js/patternfly.min.js"></script>
    <style>
      .form-horizontal .control-label.text-left {
        text-align: left;
      }
      .GKGFBNLBERB {
        font-weight: bold;
        margin-bottom: 5px;
      }
      .GKGFBNLBCNB {
        overflow-x: auto;
        margin-top: -1px;
      }
      .content-view-pf-pagination .btn-pagination {
        display: -ms-flexbox;
        display: flex;
        margin: 0 0 0 10px;
      }
      /* dataTables CSS modification & positioning */
      table.dataTable thead {
        position:relative;
        zoom:1;
      }
      
      table.dataTable thead .sorting_asc, 
      table.dataTable thead .sorting_desc {
          color: #6e7989 !important;
          position: relative;
      }
      
      table.dataTable thead .sorting:before,
      table.dataTable thead .sorting_asc:before,
      table.dataTable thead .sorting_desc:before,
      table.dataTable thead .sorting_asc_disabled:before,
      table.dataTable thead .sorting_desc_disabled:before {
        right: 0 !important;
        content: "" !important;
      }
      table.dataTable thead .sorting:after,
      table.dataTable thead .sorting_asc:after,
      table.dataTable thead .sorting_desc:after,
      table.dataTable thead .sorting_asc_disabled:after,
      table.dataTable thead .sorting_desc_disabled:after {
        right: 0 !important;
        content: "" !important;
      }
      table.dataTable thead th {
          position: relative;
          background-image: none !important;
          padding-left: 14px !important;
      }
        
      table.dataTable thead th.sorting:after,
      table.dataTable thead th.sorting_asc:after,
      table.dataTable thead th.sorting_desc:after {
          position: absolute !important;
          top: 50% !important;
          display: block !important;
          line-height: 0.0px !important;
          left: 0 !important;
          font-family: FontAwesome !important;
          font-size: 1.2em !important;
          padding-left: 4px !important;
      }
      table.dataTable thead th.sorting:after {
          content: "\f0dc" !important;
          color: rgba(255, 255, 255, 0) !important;
          font-size: 1.2em !important;
      }
      table.dataTable thead th.sorting_asc:after {
          content: "\f0de" !important;
      }
      table.dataTable thead th.sorting_desc:after {
          content: "\f0dd" !important;
      }
      table.dataTable tbody > tr.selected > td > a {
        color: white;
      }
      table.dataTable tbody > tr > td > a:hover {
        text-decoration:underline;
      }
    </style>
  </head>
  <body>
    <div class="obrand_main_tab container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb">
              <li class="active">Virtual Class</li>
              <li class="active"><a href="curriculum.html">Curriculum</a></li>
              <li class="active"><a href="javascript:;" style="font-size: 28px;" id="curriculum_name"></a></li>
            </ol>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <span>Teacher: <i id="teacher"></i>, </span>
          <span>Max number of peaple: <i id="maxnum"></i></span>
        </div>
      </div>
      <div class="toolbar-pf">
        <div class="toolbar-pf-actions">
          <div class="form-group toolbar-pf-filter">
            <div class="row">
              <div class="col-sm-12">
                <div class="input-group">
                  <span class="input-group-addon">Student:</span>
                  <input type="text" class="form-control" id="SearchPanelView_searchStringInput">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default" id="SearchPanelView_searchClean"><i class="fa fa-close"></i> </button>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editModal" id="editBtn">Edit</button>
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteModal" id="deleteBtn">Delete</button>
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#addModal" id="addBtn">Add student</button>
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#removeModal" id="removeBtn" disabled>Remove student</button>
          </div>
        </div>
      </div>
      <div class="GKGFBNLBCNB table-responsive">
        <table id="students_table" class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th><input id="checkboxRemove" type="checkbox"></th>
              <th>Account</th>
              <th>Name</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
   
    <!-- The edit Modal -->
    <div class="modal" id="editModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form class="form-horizontal needs-validation" id="editForm" novalidate>
            <!-- Modal Header -->
            <div class="modal-header">
              <button type="button" class="gwt-Button close" data-dismiss="modal">
                <span class="pficon pficon-close"></span>
              </button>
              <h4 class="modal-title">Edit Pod</h4>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
              <div class="container-fluid">
                <div class="form-group resources-policy">
                  <label class="control-label col-sm-6 text-left">Curriculum name:</label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" name="curriculum_name" id="editCurriculum_name" required>
                  </div>
                </div>
                <div class="form-group resources-policy">
                  <label class="control-label col-sm-6 text-left">Teacher:</label>
                  <div class="col-sm-6">
                    <select class="form-control" name="teacher" id="editTeacher">
                    </select>
                  </div>
                </div>
                <div class="form-group resources-policy">
                  <label class="control-label col-sm-6 text-left">Max number of people:</label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" name="maxnum" id="editMaxnum" required>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">OK</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- The delete Modal -->
    <div class="modal" id="deleteModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <button type="button" class="gwt-Button close" data-dismiss="modal">
              <span class="pficon pficon-close"></span>
            </button>
            <h4 class="modal-title">Delete Curriculum(s)</h4>
          </div>
    
          <!-- Modal body -->
          <div class="modal-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col-sm-12">
                  <div class="alert alert-warning">
                    <span class="pficon pficon-info pficon-warning-triangle-o"></span>
                    <div>Are you sure you want to delete this curriculum?<br/>It will remove all students in this curriculum.</div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12" id="deleteItems">
                </div>
              </div>
            </div>
          </div>
    
          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">OK</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- The add Modal -->
    <div class="modal" id="addModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <button type="button" class="gwt-Button close" data-dismiss="modal">
              <span class="pficon pficon-close"></span>
            </button>
            <h4 class="modal-title">Add student</h4>
          </div>
    
          <!-- Modal body -->
          <div class="modal-body">
            <div class="container-fluid">
              <h4></h4>
              <div class="row">
                <div class="col-sm-12">
                  <div class="input-group">
                    <span class="input-group-addon">Curriculums:</span>
                    <select class="form-control" name="curriculum_select" id="curriculum_select">
                      <option value="all">All</option>
                    </select>
                  </div>
                </div>
              </div>
              <hr style="margin-top: 5px; margin-bottom: 10px;"/>
              <div class="row">
                <div class="col-sm-6">
                  <div class="input-group">
                    <span class="input-group-addon">Search:</span>
                    <input type="text" class="form-control" id="addTable_search">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default" id="addTable_searchClean"><i class="fa fa-close"></i> </button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="table-responsive">          
                    <table class="table table-bordered table-striped" id="add_table">
                      <thead>
                        <tr>
                          <th><input id="checkboxAdd" type="checkbox"></th>
                          <th>Account</th>
                          <th>Name</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
    
          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Add</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- The remove Modal -->
    <div class="modal" id="removeModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <button type="button" class="gwt-Button close" data-dismiss="modal">
              <span class="pficon pficon-close"></span>
            </button>
            <h4 class="modal-title">Remove student</h4>
          </div>
    
          <!-- Modal body -->
          <div class="modal-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col-sm-12">
                  <div class="alert alert-warning">
                    <span class="pficon pficon-info pficon-warning-triangle-o"></span>
                    <div>Are you sure you want to remove the following items?</div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12" id="removeItems">
                </div>
              </div>
            </div>
          </div>
    
          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Remove</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>

  </body>
  <script type="text/javascript" src="./scripts/virtual-class.js"></script>
  <script>
    $(document).ready(function(){
      $.fn.dataTable.ext.classes.sPageButton = 'btn btn-default GKGFBNLBANB';

      parent.postMessage(VIRTUAL_CLASS_PLUGIN_MESSAGE_PREFIX + VIRTUAL_CLASS_PLUGIN_MESSAGE_DELIM + 'editUsers', '*');

      var classId = "<?php echo $classId ?>";

      var curriculum = $.ajax({
          type: "POST",
          url: "function.php?f=getCurriculum",
          data: { classId: classId },
          async: false
        }).responseText;
      var json = JSON.parse(curriculum);
      var className = json[1];
      var classTeacher = json[2];
      var classMaxnum = json[3];
      var classNum = json[4];
      
      $('#curriculum_name').html(className);
      $('#teacher').html(classTeacher);
      $('#maxnum').html(classMaxnum);

      getCurriculumStudent(classId);

      $('#editBtn').click(function(){
        $('#editCurriculum_name').val(className);
        $('#editTeacher option[value="'+classTeacher+'"]').attr("selected",true);
        $('#editMaxnum').val(classMaxnum);
      });

      $('#editForm').submit(function(event) {
        event.preventDefault();
        if (validateEditForm() != 'false') {
          editCurriculum(classId);
          location.reload();
        }
      });

      $('#deleteBtn').click(function(){
        $('#deleteItems').html("<div>- " + className + "</div>");
      });

      $('#deleteModal button[type=submit]').click(function(){
        var id = [classId];
        deleteCurriculum(id);
        window.location.href = "curriculum.html";
      });

      $('#addBtn').click(function(){
        $('#addModal .modal-body h4').html(className);
        var data = $.ajax({
          type: "GET", 
          url: "function.php?f=getCurriculumsList",
          async: false
        }).responseText;
        var json = JSON.parse(data).data;
        console.log(json);
        $('#curriculum_select').html('');
        $('#curriculum_select').append('<option value="all">All</option>');
        for (var i=0; i<json.length; i++) {
           if (json[i].id != classId) $('#curriculum_select').append('<option value="'+json[i].id+'">'+json[i].name+'</option>');
        }
        $('#checkboxAdd').prop('checked', false);
        addCurriculumList(classId, 'all');
      });

      $('#curriculum_select').change(function(){
        var select = $(this).val();
        $('#checkboxAdd').prop('checked', false);
        addCurriculumList(classId, select);
      });

      $('#checkboxAdd').click(function(){
        var table = $("#add_table").DataTable();
        $('#add_table input:checkbox').prop('checked', this.checked);
        if ($('#checkboxAdd').is(':checked'))
          table.rows().select();
        else
          table.rows().deselect();
      });

      $('#addModal button[type=submit]').click(function(){
        var students = $('#add_table td input:checkbox:checked').map(function(){
              return $(this).val();
            }).get();
        if (students.length == 0) {
          alert('Please choose student!');
        } else if (students.length + classNum > classMaxnum) {
          alert('More than the maximum number');
        } else {
          addCurriculum(classId, students);
         $('#students_table').DataTable().ajax.reload();
        }
      });

      $('#removeBtn').click(function(){
        var table = $("#students_table").DataTable();
        var rows = table.rows( { selected: true } ).indexes();
        var selects = table.rows(rows).data().toArray();
        $('#removeItems').html('');
        for (select of selects) {
          $('#removeItems').append("<div>- "+ select.account +"  " + select.name + "</div>");
        }
      });

      $('#checkboxRemove').click(function(){
        var table = $("#students_table").DataTable();
        $('#students_table input:checkbox').prop('checked', this.checked);
        if ($('#checkboxRemove').is(':checked'))
          table.rows().select();
        else
          table.rows().deselect();
      });

      $('#removeModal button[type=submit]').click(function(){
        var students = $('#students_table td input:checkbox:checked').map(function(){
              return $(this).val();
            }).get();
        if (students.length == 0) {
          alert('Please choose student!');
        } else {
          removeCurriculum(classId, students);
         $('#students_table').DataTable().ajax.reload();
         $('#checkboxRemove').prop('checked', false);
        }
      });
    });
  </script>
</html>
