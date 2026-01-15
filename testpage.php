<?php
require_once './helpers/QuestionDAO.php';
$questionDAO = new QuestionDAO();
$question = array();
$member = $questionDAO->getAll();

$names = array_column($member, 'q_number');
$string = implode('_', $names);
echo $string;
var_dump($string);

?>