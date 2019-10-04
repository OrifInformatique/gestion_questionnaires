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
        // TODO This test doesn't work, to be fixed
        $output = $this->request('GET', 'home');
        $this->assertContains(lang('page_login'), $output);
    }

    public function test_index_as_user()
    {
        $this->register_login(1);
        $this->request('GET', 'home');
        $this->assertResponseCode(200);
    }

    public function test_index_as_manager()
    {
        $this->register_login(2);
        $this->request('GET', 'home');
        $this->assertRedirect('Questionnaire');
    }

    public function test_index_as_admin()
    {
        $this->register_login(4);
        $this->request('GET', 'home');
        $this->assertRedirect('Questionnaire');
    }

    public function register_login($user_access)
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'user_test';
        $_SESSION['user_access'] = $user_access;
        $_SESSION['logged_in'] = true;
    }
}
