<?php

namespace Controllers;

use Models\Schedule;
use Views\TabletModeScheduleView;

/**
 * Class ScheduleViewController
 *
 * Manages schedules
 *
 * @package Controllers
 */
class TabletModeScheduleViewController extends Controller
{

    /**
     * @var Schedule
     */
    private $model;

    /**
     * @var TabletModeScheduleView
     */
    private $view;

    /**
     * AlertController constructor
     */
    public function __construct() {
        $this->model = new Schedule();
        $this->view = new TabletModeScheduleView();
    }

    public function displayAll() {
      return '<p>schedule</p>';
    }
}
