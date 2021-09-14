<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * asignarprocesoversion
 *
 * @ORM\Table(name="asignarprocesoversion")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\asignarprocesoversionRepository")
 */
class asignarprocesoversion
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
     * @ORM\ManyToOne(targetEntity="version")
     * @ORM\JoinColumn(name="version", referencedColumnName="id")
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255)
     */
    private $estado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date", nullable=true)
     */
    private $fecha_inicio;

    /**
     * @var string
     *
     * @ORM\Column(name="comentario", type="string", length=255, nullable=true)
     */
    private $comentario;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_lt", type="string", length=255)
     */
    private $nombre_lt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="date", nullable=true)
     */
    private $fecha_fin;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;

    /**
     * @var int
     *
     * @ORM\Column(name="tiempo_stddesp", type="integer")
     */
    private $tiempo_stddesp = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="tiempo_std", type="integer")
     */
    private $tiempo_std = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="tiempo_stddespsub", type="integer")
     */
    private $tiempo_stddespsub = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="tiempo_stdsub", type="integer")
     */
    private $tiempo_stdsub = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="ligada", type="integer", nullable=true)
     */
    private $ligada = '0';


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
     * Set estado
     *
     * @param string $estado
     *
     * @return asignarprocesoversion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set comentario
     *
     * @param string $comentario
     *
     * @return asignarprocesoversion
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
     * Set version
     *
     * @param \GestionBundle\Entity\version $version
     *
     * @return asignarprocesoversion
     */
    public function setVersion(\GestionBundle\Entity\version $version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return \GestionBundle\Entity\version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set nombreLt
     *
     * @param string $nombreLt
     *
     * @return asignarprocesoversion
     */
    public function setNombreLt($nombreLt)
    {
        $this->nombre_lt = $nombreLt;

        return $this;
    }

    /**
     * Get nombreLt
     *
     * @return string
     */
    public function getNombreLt()
    {
        return $this->nombre_lt;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return asignarprocesoversion
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fecha_inicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fecha_inicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     *
     * @return asignarprocesoversion
     */
    public function setFechaFin($fechaFin)
    {
        $this->fecha_fin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fecha_fin;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return asignarprocesoversion
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
     * Set tiempoStddesp
     *
     * @param integer $tiempoStddesp
     *
     * @return asignarprocesoversion
     */
    public function setTiempoStddesp($tiempoStddesp)
    {
        $this->tiempo_stddesp = $tiempoStddesp;

        return $this;
    }

    /**
     * Get tiempoStddesp
     *
     * @return integer
     */
    public function getTiempoStddesp()
    {
        return $this->tiempo_stddesp;
    }

    /**
     * Set tiempoStd
     *
     * @param integer $tiempoStd
     *
     * @return asignarprocesoversion
     */
    public function setTiempoStd($tiempoStd)
    {
        $this->tiempo_std = $tiempoStd;

        return $this;
    }

    /**
     * Get tiempoStd
     *
     * @return integer
     */
    public function getTiempoStd()
    {
        return $this->tiempo_std;
    }

    /**
     * Set tiempoStddespsub
     *
     * @param integer $tiempoStddespsub
     *
     * @return asignarprocesoversion
     */
    public function setTiempoStddespsub($tiempoStddespsub)
    {
        $this->tiempo_stddespsub = $tiempoStddespsub;

        return $this;
    }

    /**
     * Get tiempoStddespsub
     *
     * @return integer
     */
    public function getTiempoStddespsub()
    {
        return $this->tiempo_stddespsub;
    }

    /**
     * Set tiempoStdsub
     *
     * @param integer $tiempoStdsub
     *
     * @return asignarprocesoversion
     */
    public function setTiempoStdsub($tiempoStdsub)
    {
        $this->tiempo_stdsub = $tiempoStdsub;

        return $this;
    }

    /**
     * Get tiempoStdsub
     *
     * @return integer
     */
    public function getTiempoStdsub()
    {
        return $this->tiempo_stdsub;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return asignarprocesoversion
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

    public function __construct()
    {
        $this->fecha_inicio = new \DateTime();
    }

    /**
     * Set ligada
     *
     * @param integer $ligada
     *
     * @return asignarprocesoversion
     */
    public function setLigada($ligada)
    {
        $this->ligada = $ligada;

        return $this;
    }

    /**
     * Get ligada
     *
     * @return integer
     */
    public function getLigada()
    {
        return $this->ligada;
    }
}
