<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
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
     * Permet de signaler une erreur lorsqu'on l'utilise
     * Cela envoie dans un fichier.log la date et l'heure puis un message d'erreur
     * @param $event    Événement de l'erreur
     */
    public function addLogEvent($event){
        $time = date("D, d M Y H:i:s");
        $time = "[".$time."] ";
        $event = $time.$event."\n";
        file_put_contents(ABSPATH.TV_PLUG_PATH."fichier.log", $event, FILE_APPEND);
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
        $path = ABSPATH . TV_PLUG_PATH."controllers/fileICS/file1/".$code;
        return $path;
    }

    /**
     * Ajoute un fichier ICS via le code donné
     * @param $code     Code ADE
     */
    public function addFile($code){
        try {
            $path = $this->getFilePath($code);
            $url = $this->getUrl($code);
            //file_put_contents($path, fopen($url, 'r'));
            $contents = '';
            $handler = fopen($url, "r");
            if($handler) {
                    while(!feof($handler)) {
                        $contents .= fread($handler, 8192);
                    }
                fclose($handler);
            } else {
                throw new Exception('File open failed.');
            }
            $handlew = fopen($path, "w");
            if($handlew) {
                fwrite($handlew, $contents);
                fclose($handlew);
            } else {
                throw new Exception('File open failed.');
            }


        } catch (Exception $e) {
            $this->addLogEvent($e);
        }

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