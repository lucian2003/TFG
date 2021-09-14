<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * operacionAsignada
 *
 * @ORM\Table(name="operacion_asignada")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\operacionAsignadaRepository")
 */
class operacionAsignada
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
     * @ORM\ManyToOne(targetEntity="tareaAsignada")
     * @ORM\JoinColumn(name="id_tareaAsignada", referencedColumnName="id")
     */
    private $idTareaAsignada;

    /**
     * @ORM\ManyToOne(targetEntity="opbasica")
     * @ORM\JoinColumn(name="id_operacionbasica", referencedColumnName="id")
     */
    private $idOperacionbasica;

    /**
     * @var int
     *
     * @ORM\Column(name="repeticion", type="integer")
     */
    private $repeticion = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="tiempo", type="integer")
     */
    private $tiempo = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="comentario", type="string", length=255)
     */
    private $comentario;

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
     * Set idTareaAsignada
     *
     * @param integer $idTareaAsignada
     *
     * @return operacionAsignada
     */
    public function setIdTareaAsignada($idTareaAsignada)
    {
        $this->idTareaAsignada = $idTareaAsignada;

        return $this;
    }

    /**
     * Get idTareaAsignada
     *
     * @return int
     */
    public function getIdTareaAsignada()
    {
        return $this->idTareaAsignada;
    }

    /**
     * Set idOperacionbasica
     *
     * @param integer $idOperacionbasica
     *
     * @return operacionAsignada
     */
    public function setIdOperacionbasica($idOperacionbasica)
    {
        $this->idOperacionbasica = $idOperacionbasica;

        return $this;
    }

    /**
     * Get idOperacionbasica
     *
     * @return int
     */
    public function getIdOperacionbasica()
    {
        return $this->idOperacionbasica;
    }

    /**
     * Set repeticion
     *
     * @param integer $repeticion
     *
     * @return operacionAsignada
     */
    public function setRepeticion($repeticion)
    {
        $this->repeticion = $repeticion;

        return $this;
    }

    /**
     * Get repeticion
     *
     * @return int
     */
    public function getRepeticion()
    {
        return $this->repeticion;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return operacionAsignada
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
     * Set comentario
     *
     * @param string $comentario
     *
     * @return operacionAsignada
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set tiempo
     *
     * @param integer $tiempo
     *
     * @return operacionAsignada
     */
    public function setTiempo($tiempo)
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    /**
     * Get tiempo
     *
     * @return integer
     */
    public function getTiempo()
    {
        return $this->tiempo;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return operacionAsignada
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
