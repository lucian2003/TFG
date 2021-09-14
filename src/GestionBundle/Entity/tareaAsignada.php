<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tareaAsignada
 *
 * @ORM\Table(name="tarea_asignada")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\tareaAsignadaRepository")
 */
class tareaAsignada
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
     * @ORM\ManyToOne(targetEntity="asignarprocesoversion")
     * @ORM\JoinColumn(name="id_asignarprocesoversion", referencedColumnName="id")
     */
    private $idAsignarprocesoversion;

    /**
     * @ORM\ManyToOne(targetEntity="tarea")
     * @ORM\JoinColumn(name="id_tarea", referencedColumnName="id")
     */
    private $idTarea;

    /**
     * @var int
     *
     * @ORM\Column(name="tiempo", type="integer")
     */
    private $tiempo = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="lote", type="integer", nullable=true)
     */
    private $lote;


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
     * Set idAsignarprocesoversion
     *
     * @param integer $idAsignarprocesoversion
     *
     * @return tareaAsignada
     */
    public function setIdAsignarprocesoversion($idAsignarprocesoversion)
    {
        $this->idAsignarprocesoversion = $idAsignarprocesoversion;

        return $this;
    }

    /**
     * Get idAsignarprocesoversion
     *
     * @return int
     */
    public function getIdAsignarprocesoversion()
    {
        return $this->idAsignarprocesoversion;
    }

    /**
     * Set idTarea
     *
     * @param integer $idTarea
     *
     * @return tareaAsignada
     */
    public function setIdTarea($idTarea)
    {
        $this->idTarea = $idTarea;

        return $this;
    }

    /**
     * Get idTarea
     *
     * @return int
     */
    public function getIdTarea()
    {
        return $this->idTarea;
    }

    /**
     * Set tiempo
     *
     * @param integer $tiempo
     *
     * @return tareaAsignada
     */
    public function setTiempo($tiempo)
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    /**
     * Get tiempo
     *
     * @return int
     */
    public function getTiempo()
    {
        return $this->tiempo;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return tareaAsignada
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set lote
     *
     * @param integer $lote
     *
     * @return tareaAsignada
     */
    public function setLote($lote)
    {
        $this->lote = $lote;

        return $this;
    }

    /**
     * Get lote
     *
     * @return integer
     */
    public function getLote()
    {
        return $this->lote;
    }
}
