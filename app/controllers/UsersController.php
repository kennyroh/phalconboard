<?php
namespace Pentabot\Controllers;

use Pentabot\Models\Users;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UsersController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        echo "UsersController index";
        $this->persistent->parameters = null;
    }

    /**
     * Searches for users
     */
    public function searchAction()
    {

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Pentabot\\Models\\Users", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
//        $parameters[] = "is_banned = 0";
        $parameters["order"] = "id";
        $users = Users::softFind($parameters);
        if (count($users) == 0) {
            $this->flash->notice("The search did not find any users");

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $users,
            "limit"=> 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a user
     *
     * @param string $id
     * @return mixed
     */
    public function editAction($id)
    {

        if (!$this->request->isPost()) {

            $user = Users::findFirst(["id = $id"]);
            if (!$user) {
                $this->flash->error("user was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "users",
                    "action" => "index"
                ));
            }

            $this->view->id = $user->id;

            $this->tag->setDefault("id", $user->id);
            $this->tag->setDefault("username", $user->username);
            $this->tag->setDefault("password", $user->password);
            $this->tag->setDefault("nick", $user->nick);
            $this->tag->setDefault("email", $user->email);
            $this->tag->setDefault("created_at", $user->created_at);
            $this->tag->setDefault("modified_at", $user->modified_at);
            $this->tag->setDefault("timezone", $user->timezone);
            $this->tag->setDefault("is_banned", $user->is_banned);
            $this->tag->setDefault("is_delete", $user->is_deleted);
            $this->tag->setDefault("status", $user->status);
            
        }
        return null;
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $user = new Users();

        $user->username = $this->request->getPost("username");
        $user->password = $this->request->getPost("password");
        $user->nick = $this->request->getPost("nick");
        $user->email = $this->request->getPost("email", "email");
        $user->created_at = $this->request->getPost("created_at");
        $user->modified_at = $this->request->getPost("modified_at");
        $user->timezone = $this->request->getPost("timezone");
        $user->is_banned = $this->request->getPost("banned");
        $user->status = $this->request->getPost("status");
        

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "new"
            ));
        }

        $this->flash->success("user was created successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));

    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

//        $user = Users::findFirstByid($id);
//        $user = Users::findFirst(["id = $id"]);
        $user = new Users();
//        $user = $user->findFirst(["id = $id"]);
        if (!$user) {
            $this->flash->error("user does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $user->username = $this->request->getPost("username");
        $user->password = $this->request->getPost("password");
        $user->nick = $this->request->getPost("nick");
        $user->email = $this->request->getPost("email", "email");
        $user->created_at = $this->request->getPost("created_at");
        $user->modified_at = $this->request->getPost("modified_at");
        $user->timezone = $this->request->getPost("timezone");
        $user->is_banned = $this->request->getPost("is_banned");
        $user->status = $this->request->getPost("status");
        

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "edit",
                "params" => array($user->id)
            ));
        }

        $this->flash->success("user was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));

    }

    /**
     * Deletes a user
     *
     * @param string $id
     * @return mixed
     */
    public function deleteAction($id)
    {
//        $user = Users::findFirstByid($id);
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error("user was not found");

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "search"
            ));
        }

        $this->flash->success("user was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));
    }

}
