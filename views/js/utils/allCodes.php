<?php
/**
 * Affiche un select avec les années - groupes et demi-groupes
 */

include_once '../../../../../../wp-config.php';
include_once  '../../../models/Model.php';
include_once '../../../models/CodeAdeManager.php';

$model = new CodeAdeManager();
$years = $model->getCodeYear();
$groups = $model->getCodeGroup();
$halfgroups = $model->getCodeHalfgroup();

echo '
          <option value="0">Aucun</option>
          <optgroup label="Année">';
foreach ($years as $year) {
    echo '<option value="'.$year['code'].'">'.$year['title'].'</option >';
}
echo '</optgroup>
     <optgroup label="Groupe">';
foreach ($groups as $group){
    echo '<option value="'.$group['code'].'">'.$group['title'].'</option>';
}
echo '</optgroup>
      <optgroup label="Demi groupe">';
foreach ($halfgroups as $halfgroup){
    echo '<option value="'.$halfgroup['code'].'">'.$halfgroup['title'].'</option>';
}
echo '</optgroup>';