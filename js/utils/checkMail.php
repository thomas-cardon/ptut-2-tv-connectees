<?php

require_once( "../../../../../wp-load.php" );

$mail = $_POST['mail'];

$model = new StudentModel();

return $model->verifyMail($mail);