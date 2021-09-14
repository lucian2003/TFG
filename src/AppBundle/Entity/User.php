<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="planta", type="integer", nullable=true)
     */
    private $planta;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Set planta
     *
     * @param integer $planta
     *
     * @return User
     */
    public function setPlanta($planta)
    {
        $this->planta = $planta;

        return $this;
    }

    /**
     * Get planta
     *
     * @return integer
     */
    public function getPlanta()
    {
        return $this->planta;
    }
}
