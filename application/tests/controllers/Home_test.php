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
}
