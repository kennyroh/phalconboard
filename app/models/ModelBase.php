<?php
namespace Pentabot\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

/**
 * Created by PhpStorm.
 * User: z
 * Date: 2016-05-22
 * Time: 오후 1:27
 * 
 */
abstract class ModelBase extends Model
{
    public function initialize() {
//        echo "ModelBase initialized";
        $this->setup(array(
            'notNullValidations'=>false,
        ));
    }

    /**
     * @inheritdoc
     *
     * @access public
     * @static
     * @param array|string $parameters Query parameters
     * @return Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function softFind($parameters = null)
    {
        if (is_array($parameters)) {
            if (isset($parameters[0])) {
                $parameters[0] .= ' AND is_deleted = 0';
            } else {
                if (isset($parameters['conditions'])) {
                    $parameters['conditions'] .= ' AND is_deleted = 0';
                }else{
                    $parameters[0] = ' is_deleted = 0';
                }
            }
        }
        return parent::find($parameters);
    }

    /**
     * @inheritdoc
     *
     * @access public
     * @static
     * @param array|string $parameters Query parameters
     * @return Phalcon\Mvc\Model
     */
    public static function softFindFirst($parameters = null)
    {
        if (is_array($parameters)) {
            if (isset($parameters[0])) {
                $parameters[0] .= ' AND is_deleted = 0';
            } else {
                if (isset($parameters['conditions'])) {
                    $parameters['conditions'] .= ' AND is_deleted = 0';
                }else{
                    $parameters[0] = ' is_deleted = 0';
                }
            }
        }
        return parent::findFirst($parameters);
    }

    public function softDelete($id)
    {
//        echo call_user_func([get_called_class(), 'getTable']);
//        $query = $this->getModelsManager()->createQuery('UPDATE users SET is_deleted = 1 WHERE id = :id: limit 1:');
//        return $query->execute(array('id' => $id));
//        $query = sprintf("UPDATE users SET is_deleted = 1 WHERE id = %d limit 1", $id);
        $table = call_user_func([get_called_class(), 'getTable']);
        settype($id, 'integer'); //avoid sql injection
        $query = "UPDATE $table SET is_deleted = 1 WHERE id = $id limit 1";
        return $this->getDI()->getShared('db')->query($query);
//        $query = new Query("UPDATE users SET is_deleted = 1 WHERE id = $id limit 1", $this->getDI());
//        return $query->execute();
    }
}