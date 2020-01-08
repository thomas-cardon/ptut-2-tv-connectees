<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 29/04/2019
 * Time: 14:54
 */

abstract class ControllerG
{

    /**
     * On l'utilise pour récupérer un ID
     * @return mixed    Renvoie la dernière valeur d'une url
     */
    public function getMyIdUrl()
    {
        $urlExpl = explode('/', $_SERVER['REQUEST_URI']);
        $size = sizeof($urlExpl);
        return $urlExpl[$size - 2];
    }

    /**
     * Permet de signaler une erreur lorsqu'on l'utilise
     * Cela envoie dans un fichier.log la date et l'heure puis un message d'erreur
     * @param $event    string Événement de l'erreur
     */
    public function addLogEvent($event)
    {
        $time = date("D, d M Y H:i:s");
        $time = "[" . $time . "] ";
        $event = $time . $event . "\n";
        file_put_contents(ABSPATH . TV_PLUG_PATH . "fichier.log", $event, FILE_APPEND);
    }

    /**
     * Génère une url pour générer un fichier ICS,
     * On génère un fichier d'une semaine pour plus de sécurité si l'ADE crash
     * @param $code     Code ADE relié à l'emploi du temps voulu
     * @return string   Renvoie l'url du fichier ICS
     */
    public function getUrl($code)
    {
        $str = strtotime("now");
        $str2 = strtotime(date("Y-m-d", strtotime('now')) . " +6 day");
        $start = date('Y-m-d', $str);
        $end = date('Y-m-d', $str2);
        $url = 'https://ade-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources=' . $code . '&calType=ical&firstDate=' . $start . '&lastDate=' . $end;
        return $url;
    }

    /**
     * Récupère le chemin où ce situe le fichier ICS voulu
     * @param $code     int Code ADE de l'emploi du temps souhaité
     * @return string   Renvoie le chemin jusqu'au fichier ICS
     */
    public function getFilePath($code)
    {
        $filepath = ABSPATH . TV_ICSFILE_PATH;
        if (file_exists($filepath . "file0/" . $code) && filesize($filepath . "file0/" . $code) > 120) {
            $path = ABSPATH . TV_ICSFILE_PATH . "file0/" . $code;
        } else if (file_exists($filepath . "file1/" . $code) && filesize($filepath . "file1/" . $code) > 120) {
            $path = ABSPATH . TV_ICSFILE_PATH . "file1/" . $code;
            copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $code, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $code);
        } else if (file_exists($filepath . "file2/" . $code) && filesize($filepath . "file2/" . $code) > 120) {
            $path = $filepath . "file2/" . $code;
            copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $code, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $code);
        } else if (file_exists($filepath . "file3/" . $code) && filesize($filepath . "file3/" . $code) > 120) {
            $path = $filepath . "file3/" . $code;
            copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3/' . $code, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $code);
        } else {
            $this->addFile($code);
            $path = $filepath . "file0/" . $code;
        }
        return $path;
    }

    /**
     * Ajoute un fichier ICS via le code donné
     * @param $code     int Code ADE
     */
    public function addFile($code)
    {
        try {
            $path = ABSPATH . TV_ICSFILE_PATH . "file0/" . $code;
            $url = $this->getUrl($code);
            //file_put_contents($path, fopen($url, 'r'));
            $contents = '';
            if (($handler = @fopen($url, "r")) !== FALSE) {
                while (!feof($handler)) {
                    $contents .= fread($handler, 8192);
                }
                fclose($handler);
            } else {
                throw new Exception('File open failed.');
            }
            if ($handlew = fopen($path, "w")) {
                fwrite($handlew, $contents);
                fclose($handlew);
            } else {
                throw new Exception('File open failed.');
            }
        } catch (Exception $e) {
            $this->addLogEvent($e);
        }

    }
}