<?php namespace Views;

class CarouselView extends View
{
    private $infos = array();

    public function add($title, $content, $type): void
    {
      $img;

      switch($type) {
        case 'pdf':
          $img = '<object data="' . URL_PATH . TV_UPLOAD_PATH . $content . '#toolbar=0&page=1&navpanes=0&zoom=62&view=Fit&scrollbar=0" class="d-block w-100 h-100" type="application/pdf"></object>';
          break;
        default:
          $img = '<img loading="lazy" src="' . URL_PATH . TV_UPLOAD_PATH . $content . '" class="d-block w-100 h-100">';
      }
            
      array_push($this->infos, '
      <div class="carousel-item carousel-item-info' . (count($this->infos) == 0 ? ' active' : '') . '" style="min-height: 105%; min-width: 109%; overflow: hidden;">
        ' . $img . '
        <div class="carousel-caption d-block" style="bottom: -0.9rem">
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