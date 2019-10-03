<?php

if(!function_exists('redirect')) {
    /**
     * Exists only to rewrite the `redirect` function from codeigniter.
     *
     * @param string $uri
     * @param string $method
     * @param integer $code
     * @return void
     */
    function redirect($uri = '', $method = 'auto', $code = NULL) { }
}

trait MY_Test_Trait {

    /**************
     * MISC METHODS
     **************/
    
    /**
     * Sends a request to the form_validation.
     *
     * @param string $form_validation = The name of the function to call
     * @param array $data = The data to store in $_POST
     * @return void
     */
    private function _send_form_request(string $form_validation, array $data = [])
    {
        if(!method_exists($this, $form_validation)) {
            return;
        }

        $this->form_validation->reset_validation();
        $_POST = array();
        foreach($data as $key => $value) {
            $_POST[$key] = $value;
        }
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->{$form_validation}();
    }
    /**
     * Saves the most recent database error.
     *
     * @return void
     */
    private function _db_errors_save()
    {
        $this->_old_db_error = $this->topic_model->_database->error();
    }
    /**
     * Compares the saved error and the most recent error.
     *
     * @return boolean = True if the 2 errors are different
     */
    private function _db_errors_diff() : bool
    {
        return $this->_old_db_error != $this->topic_model->_database->error();
    }

    /***********
     * OVERRIDES
     ***********/
    /**
     * Prevents view displaying, call `parent::display_view` instead
     *
     * @param mixed $view_parts
     * @param mixed|null $data
     * @return void
     */
    public function display_view($view_parts, $data = NULL) { }
}