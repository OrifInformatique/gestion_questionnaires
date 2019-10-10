<?php
/**
 * Test of the Home.php controller
 *
 * @author     Section informatique de l'Orif
 * @copyright  2019 Orif
 * @link       https://sectioninformatique.ch
 */

class Home_test extends TestCase
{
    /**
     * Trying to display home page while not registered, should redirect
     * to login page.
     */
    public function test_unregistered()
    {
        $this->request('GET', 'home');
        $this->assertRedirect('auth/login');
    }

    /**
     * @dataProvider provider_index
     */
    public function test_index($user_access, $expected_func, $expected_arg)
    {
        $this->register_login($user_access);
        $this->request('GET', 'home');
        $this->$expected_func($expected_arg);
    }

    public function provider_index()
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');

        $data = [];

        $data['user'] = [ACCESS_LVL_USER, 'assertResponseCode', 200];
        $data['manager'] = [ACCESS_LVL_MANAGER, 'assertRedirect', 'Questionnaire'];
        $data['admin'] = [ACCESS_LVL_ADMIN, 'assertRedirect', 'Questionnaire'];

        return $data;
    }

    public function register_login($user_access)
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'user_test';
        $_SESSION['user_access'] = $user_access;
        $_SESSION['logged_in'] = true;
    }
}
