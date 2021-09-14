<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * submodelo
 *
 * @ORM\Table(name="submodelo")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\submodeloRepository")
 */
class submodelo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="planta")
     * @ORM\JoinColumn(name="id_planta", referencedColumnName="id")
     */
    private $idPlanta;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = '1';

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idPlanta
     *
     * @param string $idPlanta
     *
     * @return submodelo
     */
    public function setIdPlanta($idPlanta)
    {
        $this->idPlanta = $idPlanta;

        return $this;
    }

    /**
     * Get idPlanta
     *
     * @return string
     */
    public function getIdPlanta()
    {
        return $this->idPlanta;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return submodelo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return submodelo
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
