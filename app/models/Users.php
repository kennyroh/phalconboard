<?php
namespace Pentabot\Models;

use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Regex;
use Phalcon\Mvc\Model\Validator\StringLength;
use Phalcon\Mvc\View\Simple;


/**
 * Class Users
 * @property Simple posts
 * @method Simple getPosts($parameters=null)
 * @method static Users findFirstById(int $id)
 * @method static Users findFirstByUsername(string $username)
 * @method static Users[] find($parameters=null)
  */
class Users extends ModelBase
{
//    const DELETED = 'Y';
//
//    const NOT_DELETED = 'N';
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $nick;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var integer
     */
    public $created_at;

    /**
     *
     * @var integer
     */
    public $modified_at;

    /**
     *
     * @var string
     */
    public $timezone;

    /**
     *
     * @var integer
     */
    public $is_banned;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $is_deleted;

    /**
     * Validations and business logic
     */
    public function validation()
    {
        $trans = $this->getDI()->get("trans");
        $this->validate(
            new PresenceOf(
                array(
                    'field'    => 'username',
                    "message" => $trans->_(
                        "PresenceOf",
                        ['field' => $trans->_('username')]
                    )
                )
            )
        );
        // /^[a-z0-9]+$/i
        $this->validate(
            new Regex(
                array(
                    'field'    => 'username',
                    'pattern' => '/^[\w]+$/i',
                    "message" => $trans->_(
                        "AlphaNumeric",
                        ['field' => $trans->_('username')]
                    )
                )
            )
        );

        $this->validate(
            new StringLength(
                array(
                    'field'    => 'username',
                    'max' => 20,
                    'min' => 4,
                    "messageMaximum" => $trans->_("Max_username"),
                    "messageMinimum" => $trans->_("Max_username")
                )
            )
        );

        $this->validate(
            new StringLength(
                array(
                    'field'    => 'password',
                    'max' => 20,
                    'min' => 4,
                    "messageMaximum" => $trans->_("Max_password"),
                    "messageMinimum" => $trans->_("Max_password")
                )
            )
        );

        $this->validate(
            new Regex(
                array(
                    'field'    => 'password',
                    'pattern' => "/^[\w]+$/i",
                    "message" => $trans->_(
                        "AlphaNumeric",
                        ['field' => $trans->_('password')]
                    )
                )
            )
        );

        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                    "message" => $trans->_("Email")
                )
            )
        );

        return $this->validationHasFailed() != true;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();
        $this->hasMany('id', 'Posts', 'users_id', array('alias' => 'Posts'));
        $this->hasMany('id', 'Remember_tokens', 'users_id', array('alias' => 'Remember_tokens'));
        $this->addBehavior(
            new Timestampable(array(
                'beforeCreate' => array(
                    'field' => 'created_at'
                ),
                'beforeUpdate' => array(
                    'field' => 'modified_at'
                )
            ))
        );
        // Skips fields/columns on both INSERT/UPDATE operations
        $this->skipAttributes(
            array(
//                'password',
//                'timezone'
            )
        );

        // Skips only when inserting
        $this->skipAttributesOnCreate(
            array(
//                'created_at'
            )
        );

        // Skips only when updating
        $this->skipAttributesOnUpdate(
            array(
//                'modified_at'
            )
        );

    }

    public function beforeSave(){
        $security = $this->getDI()->get("security");
        if(!$this->timezone){
            $this->timezone = "No timezone";
        }

        $this->password = $security->hash($this->password);
    }

    public function beforeDelete()
    {
        $message = new Message("Sorry, but a robot cannot be named Peter");
        $this->appendMessage($message);
    }
    public function delete()
    {
        if($this->is_deleted == -1){ //real delete
            echo "real delete";
            return parent::delete();
        }else{
            $this->beforeDelete();
            echo "soft delete";
            return parent::softDelete($this->id);
        }
    }

    public static function getTable()
    {
        return "users";
    }


}
