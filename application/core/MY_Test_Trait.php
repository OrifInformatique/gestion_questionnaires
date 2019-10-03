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

/**
 * Trait for tests.
 * 
 * Implements:
 *  - `_send_form_request`
 *  - database error checking
 *  - an overwrite for `display_view`
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
trait MY_Test_Trait {
    /**
     * The previous model errors
     *
     * @var array
     */
    private $_old_db_error = array();

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
        $this->_old_db_error = array();
        // No need to compute the list of models on each call, it's most likely not gonna change
        static $models = NULL;
        if(is_null($models)) {
            $models = scandir(__DIR__.'/../models');
            $models = array_filter($models, function($var) {
                return preg_match('/^\w+\.php$/', $var);
            });
            $models = array_map(
                function($value) {
                    return strtolower(str_replace('.php', '', $value));
                },
                $models
            );
        }
        foreach($models as $model) {
            if(!isset($this->{$model})) continue;
            $this->_old_db_error[$model] = $this->{$model}->_database->error();
        }
    }
    /**
     * Compares the saved errors and the most recent errors.
     *
     * @return boolean = True if at least 1 error has changed since last check.
     */
    private function _db_errors_diff() : bool
    {
        // No need to compute the list of models on each call, it's most likely not gonna change
        static $models = NULL;
        if(is_null($models)) {
            $models = scandir(__DIR__.'/../models');
            $models = array_filter($models, function($var) {
                return preg_match('/^\w+\.php$/', $var);
            });
            $models = array_map(
                function($value) {
                    return strtolower(str_replace('.php', '', $value));
                },
                $models
            );
        }
        foreach($models as $model) {
            if(!isset($this->{$model})) continue;
            elseif($this->_old_db_error[$model] != $this->{$model}->_database->error()) return FALSE;
        }
        return TRUE;
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