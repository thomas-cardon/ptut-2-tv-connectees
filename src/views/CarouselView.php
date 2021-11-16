<?php namespace Views;

class CarouselView extends View
{
    private $infos = array();

    public function add($title, $content, $type): void
    {
      $data;
      
      if (!file_exists(PATH . TV_UPLOAD_PATH . $content)) {
        $data = '
          <div class="bg-warning bg-gradient w-100 h-100 text-center py-5">
            <h1>Erreur</h1>
            <p class="lead">Fichier introuvable</p>
            <br />
            <p style="text-align: left;overflow-wrap: anywhere;margin: 2rem;">
              <samp>' . PATH . TV_UPLOAD_PATH . $content . '</samp>
            </p>
          </div>';
      }
      else {
        switch($type) {
          case 'pdf':
            $data = '<object data="' . URL_PATH . TV_UPLOAD_PATH . $content . '#toolbar=0&page=1&navpanes=0&zoom=50&view=Fit&scrollbar=0" class="d-block w-100 h-100" type="application/pdf"></object>';
            break;
          default:
            $data = '<img loading="lazy" src="' . URL_PATH . TV_UPLOAD_PATH . $content . '" class="d-block w-100 h-100">';
        }
      }
            
      array_push($this->infos, '
      <div class="carousel-item carousel-item-info' . (count($this->infos) == 0 ? ' active' : '') . '">
        ' . $data . '
        <div class="carousel-caption d-block bg-black opacity-50 text-white w-100 start-0 bottom-0 py-3 pb-4" style="letter-spacing: 1.2px;">
          <h5>' . $title . '</h5>
        </div>
      </div>');
    }

    public function build(): String
    {
        $indicators = '';
        
        for($i = 0; $i < count($this->infos); ++$i) {
          $indicators .= '<button type="button" data-bs-target="#informationCarousel" data-bs-slide-to="' . $i . '"' . ($i == 0 ? 'class="active" aria-current="true"' : '') . '></button>';
        }
        
        return '
        <div id="informationCarousel" class="carousel carousel-dark slide" data-bs-ride="carousel" data-bs-touch="false" style="overflow: hidden;">
          <div class="carousel-indicators" style="top: 1px;">
            ' . $indicators . '
          </div>
          <div class="carousel-inner">
          ' . join('', $this->infos) . '
          </div>
        </div>';
    }
}
?>