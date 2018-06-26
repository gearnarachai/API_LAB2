<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

$home_url="http://127.0.0.1/API_LAB1/";

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$records_per_page =3;

$from_record_num=($records_per_page*$page)-$records_per_page;

?>