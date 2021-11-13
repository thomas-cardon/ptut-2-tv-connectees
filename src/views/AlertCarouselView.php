<?php namespace Views;

class AlertCarouselView extends View
{
    private $infos = array();

    public function add($author, $content): void
    {
      array_push($this->infos, $content);
    }

    public function build(): String
    {
        if (empty($this->infos)) return '';
        
        return '
        <div class="marquee fixed-bottom">
          <p>' . join(' — ', $this->infos) . '</p>
        </div>';
    }
}
?>