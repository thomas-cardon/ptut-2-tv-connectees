<?php
/**
 * Affiche un select avec les années - groupes et demi-groupes
 */

require_once( "../../../../../wp-load.php" );

$model = new CodeAdeModel();
$years = $model->getCodeYear();
$groups = $model->getCodeGroup();
$halfgroups = $model->getCodeHalfgroup();

echo '
          <option value="0">Aucun</option>
          <optgroup label="Année">';
foreach ($years as $year) {
    echo '<option value="' . $year['code'] . '">' . $year['title'] . '</option >';
}
echo '</optgroup>
     <optgroup label="Groupe">';
foreach ($groups as $group) {
    echo '<option value="' . $group['code'] . '">' . $group['title'] . '</option>';
}
echo '</optgroup>
      <optgroup label="Demi groupe">';
foreach ($halfgroups as $halfgroup) {
    echo '<option value="' . $halfgroup['code'] . '">' . $halfgroup['title'] . '</option>';
}
echo '</optgroup>';