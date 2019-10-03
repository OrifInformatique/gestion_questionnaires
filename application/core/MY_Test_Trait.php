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
     * @param array $arguments = Arguments to give to the function, in the order of usage
     * @return void
     */
    private function _send_form_request(string $form_validation, array $data = [], array $arguments = [])
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

        $this->{$form_validation}(...$arguments);
    }

    /**
     * Saves the most recent database errors.
     *
     * @return void
     */
    private function _db_errors_save()
    {
        $this->_old_db_error = array(
            'user_error' => $this->user_model->_database->error(),
            'user_type_error' => $this->user_type_model->_database->error(),
        );
    }
    /**
     * Compares the saved errors and the most recent errors.
     *
     * @return boolean = True if the 2 errors are different
     */
    private function _db_errors_diff() : bool
    {
        $user_err_diff = $this->_old_db_error['user_error'] != $this->user_model->_database->error();
        $user_type_err_diff = $this->_old_db_error['user_type_error'] != $this->user_type_model->_database->error();
        return $user_err_diff || $user_type_err_diff;
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