<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * conscalidad
 *
 * @ORM\Table(name="conscalidad")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\conscalidadRepository")
 */
class conscalidad
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
     * @ORM\Column(name="descripcion_es", type="string", length=255)
     */
    private $descripcionES;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_en", type="string", length=255, nullable=true)
     */
    private $descripcionEN;

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
     * @return conscalidad
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
     * Set descripcionES
     *
     * @param string $descripcionES
     *
     * @return conscalidad
     */
    public function setDescripcionES($descripcionES)
    {
        $this->descripcionES = $descripcionES;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcionES()
    {
        return $this->descripcionES;
    }

    /**
     * Set descripcionEN
     *
     * @param string $descripcionEN
     *
     * @return conscalidad
     */
    public function setDescripcionEN($descripcionEN)
    {
        $this->descripcionEN = $descripcionEN;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcionEN()
    {
        return $this->descripcionEN;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return conscalidad
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
