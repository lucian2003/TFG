<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * asignarprocesore
 *
 * @ORM\Table(name="asignarprocesore")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\asignarprocesoreRepository")
 */
class asignarprocesore
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
     * @ORM\ManyToOne(targetEntity="asignarproceso")
     * @ORM\JoinColumn(name="id_asignarproceso", referencedColumnName="id")
     */
    private $idAsignarproceso;

    /**
     * @ORM\ManyToOne(targetEntity="asignarprocesoversion")
     * @ORM\JoinColumn(name="id_asignarprocesoversion", referencedColumnName="id")
     */
    private $idAsignarprocesoversion;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;


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
     * Set idAsignarproceso
     *
     * @param \GestionBundle\Entity\asignarproceso $idAsignarproceso
     *
     * @return asignarprocesore
     */
    public function setIdAsignarproceso(\GestionBundle\Entity\asignarproceso $idAsignarproceso = null)
    {
        $this->idAsignarproceso = $idAsignarproceso;

        return $this;
    }

    /**
     * Get idAsignarproceso
     *
     * @return \GestionBundle\Entity\asignarproceso
     */
    public function getIdAsignarproceso()
    {
        return $this->idAsignarproceso;
    }

    /**
     * Set idAsignarprocesoversion
     *
     * @param \GestionBundle\Entity\asignarprocesoversion $idAsignarprocesoversion
     *
     * @return asignarprocesore
     */
    public function setIdAsignarprocesoversion(\GestionBundle\Entity\asignarprocesoversion $idAsignarprocesoversion = null)
    {
        $this->idAsignarprocesoversion = $idAsignarprocesoversion;

        return $this;
    }

    /**
     * Get idAsignarprocesoversion
     *
     * @return \GestionBundle\Entity\asignarprocesoversion
     */
    public function getIdAsignarprocesoversion()
    {
        return $this->idAsignarprocesoversion;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return asignarprocesore
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
