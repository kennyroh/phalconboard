<?php
/**
 * Created by pentabot.com
 * User: Roh Kyoung-Min
 * Date: 2016-05-25
 * Time: 오후 10:46
 */
namespace Pentabot\Auth;

use Pentabot\Models\RememberTokens;
use Pentabot\Models\Users;
use Phalcon\Mvc\User\Component;

class Auth extends Component
{
    private $trans;
    /**
     * Auth constructor.
     */
    public function __construct()
    {
        $this->trans = $this->getDI()->get("trans");
    }

    /**
     * @param $credentials
     * @throws Exception
     */
    public function check($credentials)
    {
        $user = Users::findFirstByUsername($credentials['username']);

        if ($user == false) {
            $this->registerUserThrottling(0);

            throw new Exception( $this->trans->_('Wrong user'));
        }

        if (!$this->security->checkHash($credentials['password'], $user->password)) {
            $this->registerUserThrottling($user->id);
            throw new Exception($this->trans->_('Wrong password'));
        }

        $this->checkUserStatus($user);

        $this->saveSuccessLogin($user);

        if (isset($credentials['remember'])) {
            $this->createRememberEnvironment($user);
        }

        $this->session->set('auth-identity', [
            'id' => $user->id,
            'username' => $user->username,
            'nick' => $user->nick,
        ]);
    }

    public function registerUserThrottling($int)
    {
    }

    public function checkUserStatus($user)
    {
    }

    public function saveSuccessLogin($user)
    {
    }

    /**
     * create remember me environment
     * @param $user
     */
    public function createRememberEnvironment($user)
    {
//        echo "createRememberEnvironment";
        $userAgent = $this->request->getUserAgent();
        $token = md5($user->username . $user->password . $userAgent);

        $rememberToken = new RememberTokens();
        $rememberToken->users_id = $user->id;
        $rememberToken->token = $token;
        $rememberToken->useragent = $userAgent;
        if($rememberToken->save()) {
            // RMU remember me user
            // RMT remember me token
            $this->cookies->set('RMU', $user->id, time() + 60 * 60 * 24 * 30);
            $this->cookies->set('RMT', $token, time() + 60 * 60 * 24 * 30);
//            echo "saved";
//        exit;
        }
    }

    /**
     * return current identity
     * @return array
     */
    public function getIdentity(){
        return $this->session->get('auth-identity');
    }

    /**
     * return remember me
     * @return bool
     */
    public function hasRememberMe(){
        return $this->cookies->has('RMU');
    }

    /**
     * @return null|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function loginWithRememberMe(){
        $user_id = $this->cookies->get('RMU')->getValue();
        $cookie_token = $this->cookies->get('RMT')->getValue();

        $user = Users::findFirstById($user_id);
        if($user){
            $userAgent = $this->request->getUserAgent();
            $token = md5($user->username . $user->password . $userAgent);

            if ($cookie_token == $token) {
                $rememberToken = RememberTokens::findFirst([
                    'users_id = ?0 AND token = ?1',
                    'bind' => [$user_id, $token]
                ]);
                if ($rememberToken) {
//                    var_dump($rememberToken);
                    if( (time() - (86400 * 8)) < $rememberToken->create_at ){
                        $this->session->set('auth-identity', [
                            'id' => $user->id,
                            'username' => $user->username,
                            'nick' => $user->nick,
                        ]);
                    }

                    return $this->response->redirect('users');
                }
            }
        }
        return null;
    }

    /**
     * returns session username
     * @return mixed
     */
    public function getName(){
        $identity = $this->session->get('auth-identity');
        return $identity['username'];
    }

    /**
     * removes user session
     */
    public function remove(){
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->get('RMT')) {
            $this->cookies->get('RMT')->delete();
        }
        $this->session->remove('auth-identity');
    }

    public function getUser(){
        $identity = $this->getIdentity();
        if (isset($identity['id'])) {
            $user = Users::findFirstById($identity['id']);
            if ($user == false) {
                throw new Exception('The_user_does_not_exist');
            }

            return $user;
        }
        return false;
    }
}