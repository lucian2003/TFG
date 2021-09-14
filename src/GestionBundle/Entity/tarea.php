<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tarea
 *
 * @ORM\Table(name="tarea")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\tareaRepository")
 */
class tarea
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
     * @ORM\Column(name="nombre_es", type="string", length=255)
     */
    private $nombreES;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_en", type="string", length=255, nullable=true)
     */
    private $nombreEN;

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
     * @param integer $idPlanta
     *
     * @return tarea
     */
    public function setIdPlanta($idPlanta)
    {
        $this->idPlanta = $idPlanta;

        return $this;
    }

    /**
     * Get idPlanta
     *
     * @return int
     */
    public function getIdPlanta()
    {
        return $this->idPlanta;
    }

    /**
     * Set nombreES
     *
     * @param string $nombreEs
     *
     * @return tarea
     */
    public function setNombreES($nombreES)
    {
        $this->nombreES = $nombreES;

        return $this;
    }

    /**
     * Get nombreES
     *
     * @return string
     */
    public function getNombreES()
    {
        return $this->nombreES;
    }

    /**
     * Set nombreEN
     *
     * @param string $nombreEN
     *
     * @return tarea
     */
    public function setNombreEN($nombreEN)
    {
        $this->nombreEN = $nombreEN;

        return $this;
    }

    /**
     * Get nombreEN
     *
     * @return string
     */
    public function getNombreEN()
    {
        return $this->nombreEN;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return tarea
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
