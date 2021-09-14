<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * linea
 *
 * @ORM\Table(name="linea")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\lineaRepository")
 */
class linea
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
     * @ORM\Column(name="productividad", type="string", length=255)
     */
    private $productividad = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="estaciones", type="integer")
     */
    private $estaciones;
    
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
     * @return linea
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
     * @return linea
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
     * @return linea
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
     * Set productividad
     *
     * @param string $productividad
     *
     * @return linea
     */
    public function setProductividad($productividad)
    {
        $this->productividad = $productividad;

        return $this;
    }

    /**
     * Get productividad
     *
     * @return string
     */
    public function getProductividad()
    {
        return $this->productividad;
    }

    /**
     * Set estaciones
     *
     * @param integer $estaciones
     *
     * @return linea
     */
    public function setEstaciones($estaciones)
    {
        $this->estaciones = $estaciones;

        return $this;
    }

    /**
     * Get estaciones
     *
     * @return integer
     */
    public function getEstaciones()
    {
        return $this->estaciones;
    }
}
