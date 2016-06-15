<?php
namespace Pentabot\Forms;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Created by pentabot.com
 * User: Roh Kyoung-Min
 * Date: 2016-05-25
 * Time: 오후 10:24
 */

class LoginForm extends Form{
    public function initialize(){
        $trans = $this->getDI()->get("trans");
//        $ft = $this->getDI()->get("field_trans");

        //username
        $username = new Text('username', [
           'placeholder' => 'Email'
        ]);
        $username->addValidators([
            new PresenceOf([
                "message" => $trans->_(
                    "PresenceOf",
                    ['field' => $trans->_('username')]
                )
            ])
        ]);
        $this->add($username);

        //password
        $password = new Password('password');
        $password->addValidators([
            new PresenceOf([
                "message" => $trans->_(
                    "PresenceOf",
                    ['field' => $trans->_('password')]
                )
            ])
        ]);
//        $password->clear();
        $this->add($password);

    }
}