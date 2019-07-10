<?php

function getCurriculum() {
  require('require/dbconfig.php');
  $classId = $_POST['classId'];
  if (!empty($classId)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("SELECT id, name, teacher, maxnum, COALESCE(num, 0) FROM class LEFT JOIN ( SELECT class, COUNT(student) AS num FROM class_group GROUP BY class) AS g ON class.id = g.class WHERE id = $classId;");
      $stmt->execute();
      $result = $stmt->fetch();
      echo json_encode($result);
    }
    catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }  
}

function getCurriculumsList() {
  require('require/dbconfig.php');
  $requestData = $_REQUEST;
  try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT id,name, teacher, maxnum, COALESCE(num, 0) AS num FROM class LEFT JOIN ( SELECT class, COUNT(student) AS num FROM class_group GROUP BY class) AS g ON class.id = g.class;");
    $stmt->execute();
    $totalData = $stmt->rowCount();
    $totalFiltered = $totalData;
    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nestedData=array();
      $nestedData['id'] = $row['id'];
      $nestedData['name'] = $row['name'];
      $nestedData['teacher'] = $row['teacher'];
      $nestedData['maxnum'] = $row['maxnum'];
      $nestedData['num'] = $row['num'];
      $data[] = $nestedData;
    }
    $json_data = array(
      "draw" => intval($requestData['draw']),
      "recordsTotal" => intval($totalData),
      "recordsFiltered" => intval($totalFiltered),
      "data" => $data
    );
    echo json_encode($json_data);
  }
  catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
  $conn = null;
}

function createCurriculum() {
  require('require/dbconfig.php');
  $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
  $teacher = htmlspecialchars($_POST['teacher'], ENT_QUOTES, 'UTF-8');
  $maxnum = htmlspecialchars($_POST['maxnum'], ENT_QUOTES, 'UTF-8');
  if (!empty($name) && !empty($teacher) && !empty($maxnum)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->exec("INSERT INTO class (name, teacher, maxnum) VALUES ('$name', '$teacher', $maxnum);");
      echo "ok"; 
    }
    catch(PDOException $e)
    {
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

function editCurriculum() {
  require('require/dbconfig.php');
  $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');
  $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
  $teacher = htmlspecialchars($_POST['teacher'], ENT_QUOTES, 'UTF-8');
  $maxnum = htmlspecialchars($_POST['maxnum'], ENT_QUOTES, 'UTF-8');
  if (!empty($id) && !empty($name) && !empty($teacher) && !empty($maxnum)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("UPDATE class SET name='$name', teacher='$teacher', maxnum=$maxnum WHERE id=$id;");
      $stmt->execute();
      echo "ok"; 
    }
    catch(PDOException $e)
    {
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}


function deleteCurriculum() {
  require('require/dbconfig.php');
  $id = implode(',', $_POST['id']);
  if (!empty($id)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();
      $conn->exec("DELETE FROM class_group WHERE class IN ($id);");
      $conn->exec("DELETE FROM class WHERE id IN ($id);");
      $conn->commit();
      echo "ok"; 
    }
    catch(PDOException $e)
    {
      $conn->rollback();
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

function addCurriculumList() {
  require('require/dbconfig.php');
  $requestData = $_REQUEST;
  $classId = $_POST['classId'];
  $searchId = $_POST['searchId'];
  if (!empty($classId) || !empty($searchId)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      if ($searchId == 'all') 
        $stmt = $conn->prepare("SELECT id, account, name FROM student WHERE id NOT IN (SELECT student FROM class_group WHERE class=$classId);");
      else
        $stmt = $conn->prepare("SELECT student.id, student.account, student.name FROM class,class_group,student WHERE class.id=class_group.class AND student.id=class_group.student AND class_group.class = $searchId AND class_group.student NOT IN (SELECT student FROM class_group WHERE class=$classId);");
      $stmt->execute();
      $totalData = $stmt->rowCount();
      $totalFiltered = $totalData;
      $data = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nestedData=array();
        $nestedData['id'] = $row['id'];
        $nestedData['account'] = $row['account'];
        $nestedData['name'] = $row['name'];
        $data[] = $nestedData;
      }
      $json_data = array(
        "draw" => intval($requestData['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
      );
      echo json_encode($json_data);
    }
    catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

function addCurriculum() {
  require('require/dbconfig.php');
  $classId = $_POST['classId'];
  $students = $_POST['students'];
  if (!empty($classId) || !empty($students)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();
      foreach ($students as $student) {
        $conn->exec("INSERT INTO class_group VALUES ($classId, $student);");
      }
      $conn->commit();
      echo "ok";
    }
    catch(PDOException $e)
    {
      $conn->rollback();
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

function getCurriculumStudent() {
  require('require/dbconfig.php');
  $requestData = $_REQUEST;
  $classId = $_POST['classId'];
  if (!empty($classId)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("SELECT id, account, name FROM class_group,student WHERE class_group.student = student.id AND class = $classId;");
      $stmt->execute();
      $totalData = $stmt->rowCount();
      $totalFiltered = $totalData;
      $data = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nestedData=array();
        $nestedData['id'] = $row['id'];
        $nestedData['account'] = $row['account'];
        $nestedData['name'] = $row['name'];
        $data[] = $nestedData;
      }
      $json_data = array(
        "draw" => intval($requestData['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
      );
      echo json_encode($json_data);
    }
    catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

function removeCurriculum() {
  require('require/dbconfig.php');
  $classId = $_POST['classId'];
  $students = $_POST['students'];
  if (!empty($classId) || !empty($students)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();
      foreach ($students as $student) {
        $conn->exec("DELETE FROM class_group WHERE class = $classId AND student = $student;");
      }
      $conn->commit();
      echo "ok";
    }
    catch(PDOException $e)
    {
      $conn->rollback();
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

function getCurriculumName() {
  require('require/dbconfig.php');
  try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT id, name FROM class;");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
  }
  catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
  $conn = null;
}

function addImageList() {
  require('require/dbconfig.php');
  $requestData = $_REQUEST;
  $tempId = $_POST['tempId'];
  $classId = $_POST['classId'];
  if (!empty($tempId) || !empty($classId)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("SELECT id, account, name FROM class_group,student WHERE class_group.student = student.id AND class = $classId AND student NOT IN (SELECT student FROM student_vms WHERE template_id = '$tempId');");
      $stmt->execute();
      $totalData = $stmt->rowCount();
      $totalFiltered = $totalData;
      $data = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nestedData=array();
        $nestedData['id'] = $row['id'];
        $nestedData['account'] = $row['account'];
        $nestedData['name'] = $row['name'];
        $data[] = $nestedData;
      }
      $json_data = array(
        "draw" => intval($requestData['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
      );
      echo json_encode($json_data);
    }
    catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

function createStudentVM() {
  require('require/dbconfig.php');
  $tempId = htmlspecialchars($_POST['tempId'], ENT_QUOTES, 'UTF-8');
  $tempName = htmlspecialchars($_POST['tempName'], ENT_QUOTES, 'UTF-8');
  $diskId = htmlspecialchars($_POST['diskId'], ENT_QUOTES, 'UTF-8');
  $imageId = htmlspecialchars($_POST['imageId'], ENT_QUOTES, 'UTF-8');
  $diskSize = htmlspecialchars($_POST['diskSize'], ENT_QUOTES, 'UTF-8');
  $storagedomain = htmlspecialchars($_POST['storagedomain'], ENT_QUOTES, 'UTF-8');
  $vnic = htmlspecialchars($_POST['vnic'], ENT_QUOTES, 'UTF-8');
  $students = $_POST['students'];
  if (!empty($tempId) || !empty($tempName) || !empty($diskId) || !empty($imageId) || !empty($diskSize) || !empty($storagedomain) || !empty($vnic) || !empty($students)) {
    chdir('../virtual-classroom-scripts');
    foreach ($students as $student) {
      exec("sudo ./create_student_vm.sh $tempId $tempName $diskId $imageId $diskSize $storagedomain $vnic ".$student['id']." ".$student['name']."> /dev/null 2>&1 &");
    }
    echo "ok";
  }
}

function removeStudentVMList() {
  require('require/dbconfig.php');
  $requestData = $_REQUEST;
  $tempId = $_POST['tempId'];
  if (!empty($tempId)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("SELECT vm_id, account, name FROM student_vms, student WHERE student_vms.student = student.id AND template_id = '$tempId';");
      $stmt->execute();
      $totalData = $stmt->rowCount();
      $totalFiltered = $totalData;
      $data = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nestedData=array();
        $nestedData['id'] = $row['vm_id'];
        $nestedData['account'] = $row['account'];
        $nestedData['name'] = $row['name'];
        $data[] = $nestedData;
      }
      $json_data = array(
        "draw" => intval($requestData['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
      );
      echo json_encode($json_data);
    }
    catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

function removeStudentVM() {
  require('require/dbconfig.php');
  $students = $_POST['students'];
  if (!empty($students)) {
    chdir('../virtual-classroom-scripts');
    foreach ($students as $student) {
      exec("sudo ./remove_student_vm.sh $student > /dev/null 2>&1 &");
    }
    echo "ok";
  }  
}

function getTemplateStudent() {
  require('require/dbconfig.php');
  $requestData = $_REQUEST;
  $tempId = $_POST['tempId'];
  if (!empty($tempId)) {
    try {
      $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("SELECT vm_id, account, name, vm_name, disk_id, image_id FROM student_vms, student WHERE student_vms.student = student.id AND template_id = '$tempId';");
      $stmt->execute();
      $totalData = $stmt->rowCount();
      $totalFiltered = $totalData;
      $data = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nestedData=array();
        $nestedData['id'] = $row['vm_id'];
        $nestedData['account'] = $row['account'];
        $nestedData['name'] = $row['name'];
        $nestedData['vm_name'] = $row['vm_name'];
        $nestedData['disk_id'] = $row['disk_id'];
        $nestedData['image_id'] = $row['image_id'];
        $data[] = $nestedData;
      }
      $json_data = array(
        "draw" => intval($requestData['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
      );
      echo json_encode($json_data);
    }
    catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $conn = null;
  } else {
    echo 'fail';
  }
}

if (function_exists($_GET['f'])) {
  $_GET['f']();
}
?>
