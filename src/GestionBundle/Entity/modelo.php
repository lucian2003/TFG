<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * modelo
 *
 * @ORM\Table(name="modelo")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\modeloRepository")
 */
class modelo
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
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=255, nullable=true)
     */
    private $area;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = '1';

    /**
     * @ORM\ManyToOne(targetEntity="gama")
     * @ORM\JoinColumn(name="id_gama", referencedColumnName="id")
     */
    private $idGama;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return modelo
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
     * Set idPlanta
     *
     * @param \GestionBundle\Entity\planta $idPlanta
     *
     * @return modelo
     */
    public function setIdPlanta(\GestionBundle\Entity\planta $idPlanta = null)
    {
        $this->idPlanta = $idPlanta;

        return $this;
    }

    /**
     * Get idPlanta
     *
     * @return \GestionBundle\Entity\planta
     */
    public function getIdPlanta()
    {
        return $this->idPlanta;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return modelo
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

    /**
     * Set area
     *
     * @param string $area
     *
     * @return modelo
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set idGama
     *
     * @param \GestionBundle\Entity\gama $idGama
     *
     * @return modelo
     */
    public function setIdGama(\GestionBundle\Entity\gama $idGama = null)
    {
        $this->idGama = $idGama;

        return $this;
    }

    /**
     * Get idGama
     *
     * @return \GestionBundle\Entity\gama
     */
    public function getIdGama()
    {
        return $this->idGama;
    }
}
