<?php

add_action('widgets_init', 'weather_register_widget');
function weather_register_widget()
{
    register_widget('WidgetWeather');
}

/**
 * Class WidgetWeather
 *
 * Widget for weather
 *
 */
class WidgetWeather extends WP_Widget
{
    /**
     * WidgetWeather constructor.
     */
    public function __construct()
    {
        parent::__construct(
            // widget ID
            'weather_widget',
            // widget name
            __('Weather widget', ' teleconnecteeamu_widget_domain'),
            // widget description
            array( 'description' => __('Widget qui affiche la météo', 'teleconnecteeamu_widget_domain'), )
        );
    }

    /**
     * Function of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        if (is_user_logged_in()) {
            echo '
            <script>const URL = location.origin + "' . URL_PATH . TV_PLUG_PATH . 'public/img";</script>
            <div id="weather-card" class="card d-flex align-content-center flex-wrap flex-grow-1" style="height: 13rem;">
              <div class="card-body">

                <div id="weatherCardCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-touch="false">
                  <!-- Indicators -->
                  <div class="carousel-indicators">
                    <button type="button" data-bs-target="#weatherCardCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
                    <button type="button" data-bs-target="#weatherCardCarousel" data-bs-slide-to="1"></button>
                  </div>
                  <!-- Carousel inner -->
                  <div class="carousel-inner">
                    <div id="card-0" class="carousel-item active">
                      <div class="d-flex justify-content-between my-3 pb-2">
                        <div>
                          <h2 class="display-2"><strong id="temperature">00°C</strong></h2>
                          <small class="mb-0">
                            <span id="city">Ville</span>, <span id="country">Pays</span>
                          </small>
                          <br />
                          <small class="mb-0">
                            <span id="time">HH:MM:SS</span> —
                            <span id="date">Lundi 1 septembre 1900</span>
                          </small>
                        </div>
                        <div>
                        <img id="condition-icon" width="150px" />
                        </div>
                      </div>
                    </div>
                    <div id="card-1" class="carousel-item">
                      <h3 class="text-center" style="margin-top: 1.145rem;">Prévisions par heure</h3>
                      <div class="d-flex text-center justify-content-between my-4 pb-2">
                        <div id="forecast-0" class="flex-column mx-3">
                          <h6>99°C</h6>
                          <img src="http://localhost/tv/wp-content/plugins/plugin-ecran-connecte/public/img/conditions/04n.png" width="32px" />
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                        <div id="forecast-1" class="flex-column mx-3">
                          <h6>99°C</h6>
                          <img src="http://localhost/tv/wp-content/plugins/plugin-ecran-connecte/public/img/conditions/04n.png" width="32px" />
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                        <div id="forecast-2" class="flex-column mx-3">
                          <h6>99°C</h6>
                          <img src="http://localhost/tv/wp-content/plugins/plugin-ecran-connecte/public/img/conditions/04n.png" width="32px" />
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                        <div id="forecast-3" class="flex-column mx-3">
                          <h6>99°C</h6>
                          <img src="http://localhost/tv/wp-content/plugins/plugin-ecran-connecte/public/img/conditions/04n.png" width="32px" />
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                        <div id="forecast-4" class="flex-column mx-3">
                          <h6>99°C</h6>
                          <img src="http://localhost/tv/wp-content/plugins/plugin-ecran-connecte/public/img/conditions/04n.png" width="32px" />
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>';
        }
    }

    /**
     * @param array $instance
     *
     * @return string|void
     */
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance[ 'title' ];
        } else {
            $title = __('Default Title', 'teleconnectee_widget_domain');
        }

        echo '
        <p>
            <label for="'.$this->get_field_id('title').'">';
        _e('Title:');
        echo '</label>
            <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" />
        </p>';
    }

    /**
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}
