<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



require_once("config/db.php");
require_once("cmd/exec.php");
$db = new Database();
$strConn = $db->getConnection();
$strExe = new ExecSQL($strConn);

$action = $_GET['cmd'];

/*
$data = json_decode(file_get_contents("php://input"));
$withdraw_id = $data->withdraw_id;
$emp_code = $data->emp_code;
$withdraw_date = $data->withdraw_date;
$price = $data->price;
$status = $data->status;
*/


$search = $_GET['search'];



$emp_code = $_GET['emp_code'];

$withdraw_id = $_GET['withdraw_id'];
/*
$withdraw_date = $_GET['withdraw_date'];
$price = $_GET['price'];
$status = $_GET['status'];
*/

switch($action){

    case "select" :


    $sql = " SELECT withdraw.*,concat(initial.initial_name,' ',employee.name_thai,' ',employee.lastname_thai) 
    as employee_name FROM withdraw
    LEFT JOIN employee
    ON withdraw.emp_code = employee.emp_code
    LEFT JOIN initial
    ON employee.initial_id = initial.initial_code ";

    if($withdraw_id!=null){
        $sql .= " WHERE withdraw.withdraw_id = '".$withdraw_id."'";
    }
    else if($search!=null){
        $sql .= " WHERE withdraw.emp_code LIKE '%".$search."%'";
    } 

    $stmt = $strExe->read($sql);
    $rowCount= $stmt->rowCount();
    $data_arr['rs'] = array();

    if($rowCount>0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            array_push($data_arr["rs"],$row);
        }

        //array_push($data_arr["rs"],$search);
    }else{
        
        array_push($data_arr["rs"],array("message"=>"ไม่มีข้อมูล"));
       
    }
   echo json_encode($data_arr);
  
    break;

    case "create" :

    $sql = " SELECT employee.emp_code,
    concat(initial.initial_name,' ',employee.name_thai,' ',employee.lastname_thai) as employee_name,
    department.dept_name as department,
    division.division_name as division,
    position.position_name as position,
    employee.salary
    FROM employee
    LEFT JOIN initial
    ON employee.initial_id = initial.initial_code 
    LEFT JOIN department
    ON department.dept_code = employee.dept_code
    LEFT JOIN division
    ON division.division_code = employee.division_code
    LEFT JOIN position
    ON position.position_code = employee.position_code 
    WHERE employee.emp_code = '".$emp_code."' ";

    

    $stmt = $strExe->read($sql);
    $rowCount= $stmt->rowCount();
    $data_arr['rs'] = array();

    if($rowCount>0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            array_push($data_arr["rs"],$row);
        }

        //array_push($data_arr["rs"],$search);
    }else{
        
        array_push($data_arr["rs"],array("message"=>"ไม่มีข้อมูล"));
       
    }
   echo json_encode($data_arr);
  
    break;


    case "insert" :

    $data = json_decode(file_get_contents("php://input"));
    $withdraw_id = $data->withdraw_id;
    $emp_code = $data->emp_code;
    $withdraw_date = $data->withdraw_date;
    $price = $data->price;
    $status = "0";
    

    if($emp_code!=null ||  $withdraw_id!=null){

   
    if($withdraw_id!=null){
    $sql = " UPDATE withdraw 
    SET  withdraw_date = '".$withdraw_date."', price = '".$price."'
    WHERE withdraw.withdraw_id = '".$withdraw_id."' ";
    }else{
    $sql = " INSERT INTO withdraw ( emp_code, withdraw_date, price, status)
    VALUES ('".$emp_code."', '".$withdraw_date."', '".$price."', '".$status."') ";
    }

    $stmt = $strExe->dataTransection($sql);

    if ($stmt == 1) {
        echo json_encode(['status' => 'ok','message' => 'บันทึกข้อมูลเรียบร้อยแล้ว']);
    } else {
        echo json_encode(['status' => 'error','message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
    }


    }else {
        echo json_encode(['status' => 'no','message' => 'กรอกข้อมูลไห้ครบ']);
    }

    break;

}


?>