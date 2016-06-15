<?php
namespace Pentabot\Controllers;

use Pentabot\Forms\LoginForm;
use Pentabot\Auth\Exception as AuthException;

class SessionController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->disable();
        return $this->response->redirect();
    }

    public function indexRedirect(){
        return $this->response->redirect('posts');
    }

    public function loginAction(){
//        \PhalconDebug::addMessage('SessionController', 'loginAction');
        $form = new LoginForm();
//        $request = $this->request;
        $identity = $this->auth->getIdentity();
        try{
            if (!$this->request->isPost()) {
                if ($this->auth->hasRememberMe()) {
//                    echo "hasRememberMe";
                    return $this->auth->loginWithRememberMe();
                }
                if (is_array($identity)) {
//                    echo "logined";
//                    return $this->response->redirect('users');
                }
            }else{
                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }else{
                    $this->auth->check([
                        'username' => $this->request->getPost('username'),
                        'password' => $this->request->getPost('password'),
                        'remember' => $this->request->getPost('remember')
                    ]);
                    return $this->response->redirect('users');
                }
            }
        }catch (AuthException $e){
            $this->flash->error($e->getMessage());
        }
    }
    
    public function logoutAction(){
        $this->auth->remove();
        return $this->response->redirect('posts');
    }

}

