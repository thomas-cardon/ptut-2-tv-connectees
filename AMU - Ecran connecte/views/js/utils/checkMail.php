<?php

require_once("../../../../../../wp-load.php");

$mail = $_POST['mail'];

$model = new StudentManager();

return $model->verifyMail($mail);