<?php


class Admin extends User
{

    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new AdminModel();
        $this->view = new AdminView();
    }

    public function changeMyWebsite() {
        $results = $this->model->getModif('column');
        $col = $results[0]->content;
        $results = $this->model->getModif('hideNoSchedule');
        $message = $results[0]->content;
        $validCol = $_POST['columnValid'];
        $validMessage = $_POST['messageValid'];
        if ($validCol) {
            $col = $_POST['column'];
            $this->model->getModif('column');
            if ($results[0]->content != $col) {
                if ($this->model->updateModif('column', $col)) {
                    $this->view->displayModificationValidate();
                } else {
                    $this->view->displayErrorToChange();
                }
            } else {
                $this->view->displayAlreadyRegister();
            }
        }

        if ($validMessage) {
            $message = $_POST['message'];
            $results = $this->model->getModif('hideNoSchedule');
            if ($results[0]->content != $message) {
                if ($this->model->updateModif('hideNoSchedule', $message)) {
                    $this->view->displayModificationValidate();
                } else {
                    $this->view->displayErrorToChange();
                }
            } else {
                $this->view->displayAlreadyRegister();
            }
        }

        return $this->view->displayFormChangeModel($col).$this->view->displayFormChangeMsgTv($message);
    }
}