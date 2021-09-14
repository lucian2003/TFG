<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * modelosubmodelo
 *
 * @ORM\Table(name="modelosubmodelo")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\modelosubmodeloRepository")
 */
class modelosubmodelo
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
     * @ORM\ManyToOne(targetEntity="modelo")
     * @ORM\JoinColumn(name="id_modelo", referencedColumnName="id")
     */
    private $idModelo;

    /**
     * @ORM\ManyToOne(targetEntity="submodelo")
     * @ORM\JoinColumn(name="id_submodelo", referencedColumnName="id")
     */
    private $idSubmodelo;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=true)
     */
    private $cantidad;


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
     * Set idModelo
     *
     * @param integer $idModelo
     *
     * @return modelosubmodelo
     */
    public function setIdModelo($idModelo)
    {
        $this->idModelo = $idModelo;

        return $this;
    }

    /**
     * Get idModelo
     *
     * @return int
     */
    public function getIdModelo()
    {
        return $this->idModelo;
    }

    /**
     * Set idSubmodelo
     *
     * @param integer $idSubmodelo
     *
     * @return modelosubmodelo
     */
    public function setIdSubmodelo($idSubmodelo)
    {
        $this->idSubmodelo = $idSubmodelo;

        return $this;
    }

    /**
     * Get idSubmodelo
     *
     * @return int
     */
    public function getIdSubmodelo()
    {
        return $this->idSubmodelo;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return modelosubmodelo
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }
}
