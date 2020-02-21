<?php

/**
 * Trait made for easily reusable functions
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
trait Test_Trait {
    /**
     * Stores the most recent errors recorded
     *
     * @var array
     */
    private $_old_db_errors = [];
    /**
     * Records the most recent database errors for each model, in case they
     * use different database connections.
     *
     * @return void
     */
    private function _db_errors_save()
    {
        $models = $this->_get_model_list();
        $CI =& get_instance();

        foreach($models as $model) {
            if(isset($CI->{$model})) {
                $this->_old_db_errors[$model] = $CI->{$model}->_database->error();
            }
        }
    }
    /**
     * Compares the most recent database errors with the recorded ones.
     * Returns FALSE if the previous errors are the same as the current ones
     *
     * @return boolean
     */
    private function _db_errors_diff() : bool
    {
        $models = $this->_get_model_list();
        $CI =& get_instance();

        foreach($models as $model) {
            if(isset($CI->{$model})) {
                if(isset($this->_old_db_errors[$model]) && $this->_old_db_errors[$model] != $CI->{$model}->_database->error()) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    /**
     * Generates a list of all models that exist in application/models
     *
     * @return array
     */
    private function _get_model_list() : array
    {
        static $models;
        if(!isset($models)) {
            $models = scandir(APPPATH.'models');
            $models = array_map(function($val) {
                return strtolower(preg_replace(['/\..*/', '/index/'], '', $val));
            }, $models);
            $models = array_filter($models, function($var) {
                return (substr($var, -6) === '_model');
            });
        }
        return $models;
    }
    /**
     * 'Logs in' with the specified access level
     *
     * @param integer $access_level = Access level to log in with. Set to 0 to
     *      log out
     * @return void
     */
    private function _login_as(int $access_level)
    {
        $_SESSION = [];
        
        session_reset();
        session_unset();

        $_SESSION['logged_in'] = (bool)$access_level;
        $_SESSION['user_access'] = $access_level;
    }
    /**
     * Destroys the contents of $_SESSION and resets the session
     *
     * @return void
     */
    private function _logout()
    {
        $_SESSION = [];
        session_reset();
    }
}