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
            <script>
              const weather = {
                ASSETS_URL: location.origin + "' . URL_PATH . TV_PLUG_PATH . 'public/img",
                lon: "' . WEATHER_LONGITUDE . '",
                lat: "' . WEATHER_LATITUDE . '",
                api_key: "' . WEATHER_API_KEY . '"
              };
            </script>
            <div id="weather-card" class="flex-wrap card d-flex align-content-around flex-grow-1" style="height: 12rem;">
              <div class="card-body" style="width: 100%"  >

                <div id="weatherCardCarousel" class="carousel slide" style="height: 100%" data-bs-ride="carousel" data-bs-touch="false" data-bs-interval="10000">
                  <!-- Indicators -->
                  <div class="mb-0 carousel-indicators">
                    <button type="button" data-bs-target="#weatherCardCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
                    <button type="button" data-bs-target="#weatherCardCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#weatherCardCarousel" data-bs-slide-to="2"></button>
                  </div>
                  <!-- Carousel inner -->
                  <div class="flex-col carousel-inner d-flex align-items-center" style="height: 100%">
                    <div id="card-0" class="carousel-item active" style="height: 12rem;">
                      <div class="py-2 d-flex justify-content-evenly">
                        <div>
                          <h2 class="display-1"><strong id="temperature">5°C</strong></h2>
                          <small class="mb-0 fs-6">
                            <span id="wind">0 KM/H</span>&nbsp;—&nbsp;
                            <span id="humidity">80% humidité</span>
                          </small>
                          <br />
                          <small class="mb-0 fs-6">
                            Le soleil se couche à <span id="sunset"></span>
                          </small>
                        </div>
                        <div>
                        <img id="condition-icon" width="150px" />
                        </div>
                      </div>
                    </div>
                    <div id="card-1" class="carousel-item" style="height: 12rem;">
                      <h3 class="my-1 text-center fs-2">Prévisions par heure</h3>
                      <div class="py-2 my-3 text-center d-flex justify-content-between">
                        <div id="forecast-h0" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                        <div id="forecast-h1" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                        <div id="forecast-h2" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                        <div id="forecast-h3" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                        <div id="forecast-h4" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0"><strong>25:00</strong></p>
                        </div>
                      </div>
                    </div>
                    <div id="card-2" class="carousel-item" style="height: 12rem;">
                      <h3 class="my-1 text-center fs-2">Prévisions par jour</h3>
                      <div class="py-2 my-3 text-center d-flex justify-content-between">
                        <div id="forecast-d0" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0 text-capitalize"><strong>Lun.</strong></p>
                        </div>
                        <div id="forecast-d1" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0 text-capitalize"><strong>Mar.</strong></p>
                        </div>
                        <div id="forecast-d2" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0 text-capitalize"><strong>Mer.</strong></p>
                        </div>
                        <div id="forecast-d3" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0 text-capitalize"><strong>Jeu.</strong></p>
                        </div>
                        <div id="forecast-d4" class="mx-1 flex-column">
                          <h6>99°C</h6>
                          <div class="forecast-icon"></div>
                          <p class="mb-0 text-capitalize"><strong>Ven.</strong></p>
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
