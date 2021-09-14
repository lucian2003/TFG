<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * asignarproceso
 *
 * @ORM\Table(name="asignarproceso")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\asignarprocesoRepository")
 */
class asignarproceso
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
     * @ORM\ManyToOne(targetEntity="submodelo")
     * @ORM\JoinColumn(name="id_submodelo", referencedColumnName="id")
     */
    private $idSubmodelo;

    /**
     * @ORM\ManyToOne(targetEntity="linea")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id")
     */
    private $idLinea;

    /**
     * @ORM\ManyToOne(targetEntity="proceso")
     * @ORM\JoinColumn(name="id_proceso", referencedColumnName="id")
     */
    private $idProceso;

    /**
     * @var string
     *
     * @ORM\Column(name="productividad", type="decimal", precision=10, scale=0)
     */
    private $productividad = '0';

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
     * Set idSubmodelo
     *
     * @param string $idSubmodelo
     *
     * @return asignarproceso
     */
    public function setIdSubmodelo($idSubmodelo)
    {
        $this->idSubmodelo = $idSubmodelo;

        return $this;
    }

    /**
     * Get idSubmodelo
     *
     * @return string
     */
    public function getIdSubmodelo()
    {
        return $this->idSubmodelo;
    }

    /**
     * Set idLinea
     *
     * @param string $idLinea
     *
     * @return asignarproceso
     */
    public function setIdLinea($idLinea)
    {
        $this->idLinea = $idLinea;

        return $this;
    }

    /**
     * Get idLinea
     *
     * @return string
     */
    public function getIdLinea()
    {
        return $this->idLinea;
    }

    /**
     * Set idProceso
     *
     * @param string $idProceso
     *
     * @return asignarproceso
     */
    public function setIdProceso($idProceso)
    {
        $this->idProceso = $idProceso;

        return $this;
    }

    /**
     * Get idProceso
     *
     * @return string
     */
    public function getIdProceso()
    {
        return $this->idProceso;
    }

    /**
     * Set productividad
     *
     * @param string $productividad
     *
     * @return asignarproceso
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
     * Set active
     *
     * @param boolean $active
     *
     * @return asignarproceso
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
