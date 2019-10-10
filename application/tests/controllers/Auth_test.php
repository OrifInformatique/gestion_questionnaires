<?php
/**
 * Test of the Home.php controller
 *
 * @author     Section informatique de l'Orif
 * @copyright  2019 Orif
 * @link       https://sectioninformatique.ch
 */

class Auth_test extends TestCase
{
    private $username = 'user';
    private $password = 'OrifInfo2009';

    /**
     * @dataProvider provider_login
     */
    public function test_login($post, $login_expected)
    {
        $this->request('POST', 'auth/login');
        if($login_expected){
            $this->assertResponseCode(200);
        } else {
            $this->assertRedirect('auth/login');
        }
    }

    public function provider_login()
    {
        $this->resetInstance();

        $data = [];

        $data['no_error'] = [
            [
                'btn_login' => 1,
                'username' => $this->username,
                'password' => $this->password
            ], [true]
        ];

        $data['no_login'] = [
            [], [false]
        ];

        $data['bad_password'] = [
            [
                'btn_login' => 1,
                'username' => $this->username,
                'password' => $this->password.'-'
            ], [false]
        ];

        $data['bad_username'] = [
            [
                'btn_login' => 1,
                'username' => $this->username.'-',
                'password' => $this->password
            ], [false]
        ];

        return $data;
    }

}
