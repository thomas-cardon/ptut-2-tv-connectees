<?php


class AdminModel extends UserModel
{
    /**
     * Renvoie le tuple lié au titre
     * @param $title    string titre de la modification
     * @return array
     */
    public function getModif($title) {
        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM ecran_modification WHERE title = '$title'"));
        return $results;
    }

    public function updateModif($title, $content) {
        global $wpdb;
        $data = ['content' => $content];
        $where = ['title' => $title];
        return $wpdb->update('ecran_modification', $data, $where);
    }
}