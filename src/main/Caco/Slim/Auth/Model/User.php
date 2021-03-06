<?php
namespace Caco\Slim\Auth\Model;

use \Caco\MiniAR;
use Caco\SaltGenerator;
use PDO;

/**
 * Class User
 * @package Caco\Slim\Auth
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class User extends MiniAR
{
    /**
     * @var string
     */
    public $userName;

    /**
     * @var string
     */
    protected $hash;

    /**
     * Sets a password for the current user.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $salt = (new SaltGenerator)->generate(32);

        $this->hash = crypt($password, sprintf('$2y$10$%s$', $salt));
    }

    /**
     * Reads a user from the database.
     *
     * @param string $userName
     * @param string $select
     * @return bool
     */
    public function read($userName, $select = null)
    {
        $query = sprintf('SELECT %s FROM `%s` WHERE `userName` = ? LIMIT 1;', $this->getFieldList(), $this->getTableName());
        $sth   = $this->pdo->prepare($query);

        $sth->execute([$userName]);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return false;
        }

        foreach ($result[0] as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    /**
     * Checks if the given password is valid for the user.
     *
     * @param string $password
     * @return bool
     */
    public function isValid($password)
    {
        return $this->hash == crypt($password, $this->hash);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return empty($this->userName) || !is_string($this->userName) ? '' : $this->userName;
    }
}