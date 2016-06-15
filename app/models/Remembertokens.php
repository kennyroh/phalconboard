<?php
namespace Pentabot\Models;

/**
 * Class RememberTokens
 * @method static RememberTokens findFirstById(int $id)
 * @method static RememberTokens findFirstByToken(string $token)
 * @method static RememberTokens[] find($parameters=null)
 */
class RememberTokens extends ModelBase
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $users_id;

    /**
     *
     * @var string
     */
    public $token;

    /**
     *
     * @var string
     */
    public $useragent;

    /**
     *
     * @var integer
     */
    public $create_at;

    public function beforeValidationOnCreate(){
        $this->create_at = time();
    }
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('users_id', __NAMESPACE__ . '\Users', 'id', array('alias' => 'Users'));
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'users_id' => 'users_id', 
            'token' => 'token', 
            'useragent' => 'useragent', 
            'create_at' => 'create_at'
        );
    }

}
