<?php
require_once './helpers/QuestionDAO.php';

session_start();




        $questionDAO = new QuestionDAO();
        $question = array();
        $member = $questionDAO->getAll();
        // $member2 = $questionDAO->insertData($question);
        print_r($member);
        $array = [1, 2, 3, 4];
$str = implode('_', $array);
        print($str);


?>