<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * opbasica
 *
 * @ORM\Table(name="opbasica")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\opbasicaRepository")
 */
class opbasica
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
     * @ORM\ManyToOne(targetEntity="proceso")
     * @ORM\JoinColumn(name="id_proceso", referencedColumnName="id")
     */
    private $idProceso;

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
     * @var int
     *
     * @ORM\Column(name="tiempo", type="integer")
     */
    private $tiempo;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="comentario_es", type="string", length=255, nullable=true)
     */
    private $comentarioES;

    /**
     * @var string
     *
     * @ORM\Column(name="comentario_en", type="string", length=255, nullable=true)
     */
    private $comentarioEN;

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
     * Set idProceso
     *
     * @param string $idProceso
     *
     * @return opbasica
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
     * Set nombreES
     *
     * @param string $nombreES
     *
     * @return opbasica
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
     * @return opbasica
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
     * Set descripcionES
     *
     * @param string $descripcionES
     *
     * @return opbasica
     */
    public function setDescripcionES($descripcionES)
    {
        $this->descripcionES = $descripcionES;

        return $this;
    }

    /**
     * Get descripcionES
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
     * @return opbasica
     */
    public function setDescripcionEN($descripcionEN)
    {
        $this->descripcionEN = $descripcionEN;

        return $this;
    }

    /**
     * Get descripcionES
     *
     * @return string
     */
    public function getDescripcionEN()
    {
        return $this->descripcionEN;
    }

    /**
     * Set tiempo
     *
     * @param integer $tiempo
     *
     * @return opbasica
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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return opbasica
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set comentarioES
     *
     * @param string $comentarioES
     *
     * @return opbasica
     */
    public function setComentarioES($comentarioES)
    {
        $this->comentarioES = $comentarioES;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string
     */
    public function getComentarioES()
    {
        return $this->comentarioES;
    }

    /**
     * Set comentarioEN
     *
     * @param string $comentarioEN
     *
     * @return opbasica
     */
    public function setComentarioEN($comentarioEN)
    {
        $this->comentarioEN = $comentarioEN;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string
     */
    public function getComentarioEN()
    {
        return $this->comentarioEN;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return opbasica
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
