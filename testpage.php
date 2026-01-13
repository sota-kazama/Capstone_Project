<?php
require_once './helpers/QuestionDAO.php';

session_start();




        $questionDAO = new QuestionDAO();
        $question = array();
        $member = $questionDAO->getAll();
        $member2 = $questionDAO->insertData($question);
        var_dump($member);
        print_r($member2);


?>