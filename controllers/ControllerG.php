<?php
/**
 * Created by PhpStorm.
 * User: Rohrb
 * Date: 29/04/2019
 * Time: 14:54
 */

abstract class ControllerG {

    /**
     * On l'utilise pour récupérer un ID
     * @return mixed    Renvoie la dernière valeur d'une url
     */
    public function getMyIdUrl(){
        $urlExpl = explode('/', $_SERVER['REQUEST_URI']);
        $size = sizeof($urlExpl);
        return $urlExpl[$size-2];
    }

    /**
     * Supprime tout les utilisateurs sélectionnés via des checkboxs
     */
    public function deleteUsers(){
        $actionDelete = $_POST['Delete'];
        $roles = ['etu','teacher','direc','tech','secre','tele'];
        if(isset($actionDelete)){
            foreach ($roles as $role) {
                if(isset($_REQUEST['checkboxstatus'.$role])) {
                    $checked_values = $_REQUEST['checkboxstatus'.$role];
                    foreach($checked_values as $val) {
                        $this->deleteUser($val);
                    }
                }
            }
        }
    }

    /**
     * Supprime l'utilisateur, si c'est un enseignant, on supprime son fichier ICS et ses alertes
     * De même pour un secrétaire ou un administrateur, on supprime les alertes & informations postés par ces-derniers
     * @param $id   ID de la personne a supprimer
     */
    public function deleteUser($id){
        $model = new StudentManager();
        $user = $model->getById($id);
        $data = get_userdata($id);
        $model->deleteUser($id);
        if(in_array("enseignant", $data->roles) == 'enseignant' ){
            $code = unserialize($user[0]['code']);
            unlink($this->getFilePath($code[0]));
        }
        if(in_array("enseignant", $data->roles) || in_array("secretaire", $data->roles) || in_array("administrator", $data->roles)){
            $modelAlert = new AlertManager();
            $modelInfo = new InformationManager();
            $alerts = $modelAlert->getListAlertByAuthor($user[0]['user_login']);
            if(isset($alerts)){
                foreach ($alerts as $alert) {
                    $modelAlert->deleteAlertDB($alert['ID_alert']);
                }
            }
            if(in_array("secretaire", $data->roles) || in_array("administrator", $data->roles)) {
                $infos = $modelInfo->getListInformationByAuthor($user[0]['user_login']);
                if(isset($infos)){
                    foreach ($infos as $info) {
                        $type = $info['type'];
                        if($type == "img" || $type == "") {
                            $this->deleteFile($info['ID_info']);
                        }
                        $modelInfo->deleteInformationDB($info['ID_info']);
                    }
                }
            }
        }
    }

    /**
     * Permet de signaler une erreur lorsqu'on l'utilise
     * Cela envoie dans un fichier.log la date et l'heure puis un message d'erreur
     * @param $event    Événement de l'erreur
     */
    public function addLogEvent($event){
        $time = date("D, d M Y H:i:s");
        $time = "[".$time."] ";
        $event = $time.$event."\n";
        file_put_contents(ABSPATH."/wp-content/plugins/TeleConnecteeAmu/fichier.log", $event, FILE_APPEND);
    }

    /**
     * Génère une url pour générer un fichier ICS,
     * On génère un fichier d'une semaine pour plus de sécurité si l'ADE crash
     * @param $code     Code ADE relié à l'emploi du temps voulu
     * @return string   Renvoie l'url du fichier ICS
     */
    public  function getUrl($code){
        $str = strtotime("now");
        $str2 = strtotime(date("Y-m-d", strtotime('now')) . " +6 day");
        $start =  date('Y-m-d',$str);
        $end = date('Y-m-d',$str2);
        $url = 'https://ade-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources='.$code.'&calType=ical&firstDate='.$start.'&lastDate='.$end;
        return $url;
    }

    /**
     * Récupère le chemin où ce situe le fichier ICS voulu
     * @param $code     Code ADE de l'emploi du temps souhaité
     * @return string   Renvoie le chemin jusqu'au fichier ICS
     */
    public function getFilePath($code){
        $path = ABSPATH . "/wp-content/plugins/TeleConnecteeAmu/controllers/fileICS/".$code;
        return $path;
    }

    /**
     * Ajoute un fichier ICS via le code donné
     * @param $code     Code ADE
     */
    public function addFile($code){
        $path = $this->getFilePath($code);
        $url = $this->getUrl($code);
        //file_put_contents($path, fopen($url, 'r'));
        $handler = fopen($url, "r");
        $contents = '';
        if($handler)
            while(!feof($handler))
                $contents .= fread($handler, 8192);
        fclose($handler);
        $handlew = fopen($path, "w");
        fwrite($handlew, $contents);
        fclose($handlew);
    }

    /**
     * Supprime le fichier lié au code
     * @param $code     Code ADE
     */
    public function deleteFile($code){
        $path = $this->getFilePath($code);
        if(! unlink($path))
            $this->addLogEvent("Le fichier ne s'est pas supprimer (chemin: ".$path.")");
    }
}