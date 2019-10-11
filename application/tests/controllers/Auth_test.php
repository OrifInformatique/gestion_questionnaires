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
    private $dummy_id;
    private $username = 'user';
    private $password = 'OrifInfo2009';


    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('user_model');
        $this->dummy_user_create();
    }
    public function tearDown()
    {
        parent::tearDown();
        $this->dummy_user_delete();
    }

    /**
     * @dataProvider provider_login
     */
    public function test_login(array $post, bool $login_expected, array $wrong_messages = [])
    {
        $output = $this->request('POST', 'auth/login', $post);
        if($login_expected){
            $this->assertRedirect('');
        } else {
            foreach ($wrong_messages as $wrong_message) {
                $this->assertContains($wrong_message, $output);
            }
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
            ], true
        ];

        $data['no_login'] = [
            ['btn_login' => 1], false, ['Le champ Identifiant est requis.', 'Le champ Mot de passe est requis.']
        ];

        $data['bad_password'] = [
            [
                'btn_login' => 1,
                'username' => $this->username,
                'password' => $this->password.'-'
            ], false, ['L\'identifiant et le mot de passe ne sont pas valides']
        ];

        $data['bad_username'] = [
            [
                'btn_login' => 1,
                'username' => $this->username.'-',
                'password' => $this->password
            ], false, ['L\'identifiant et le mot de passe ne sont pas valides']
        ];

        return $data;
    }

    public function dummy_user_create(){
        $this->dummy_id = $this->CI->user_model->insert([
            'User' => $this->username,
            'Password' => password_hash($this->password, PASSWORD_HASH_ALGORITHM),
            'FK_User_Type' => 1
        ]);
    }

    public function dummy_user_delete(){
        $this->CI->user_model->delete($this->dummy_id);
    }

    public function set_session()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'user_test';
        $_SESSION['user_access'] = ACCESS_LVL_USER;
        $_SESSION['logged_in'] = true;
    }
}
