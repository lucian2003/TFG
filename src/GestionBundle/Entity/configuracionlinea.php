<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * configuracionlinea
 *
 * @ORM\Table(name="configuracionlinea")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\configuracionlineaRepository")
 */
class configuracionlinea
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
     * @var int
     *
     * @ORM\Column(name="operarios", type="integer")
     */
    private $operarios;

    /**
     * @var int
     *
     * @ORM\Column(name="estaciones", type="integer")
     */
    private $estaciones;

    /**
     * @ORM\ManyToOne(targetEntity="version")
     * @ORM\JoinColumn(name="version", referencedColumnName="id")
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=true)
     */
    private $estado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date", nullable=true)
     */
    private $fecha_inicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="date", nullable=true)
     */
    private $fecha_fin;

    /**
     * @var int
     *
     * @ORM\Column(name="tack_teorico", type="integer")
     */
    private $tackTeorico = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="eq_semana", type="string", length=255)
     */
    private $eqSemana = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;


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
     * Set operarios
     *
     * @param integer $operarios
     *
     * @return configuracionlinea
     */
    public function setOperarios($operarios)
    {
        $this->operarios = $operarios;

        return $this;
    }

    /**
     * Get operarios
     *
     * @return int
     */
    public function getOperarios()
    {
        return $this->operarios;
    }

    /**
     * Set estaciones
     *
     * @param integer $estaciones
     *
     * @return configuracionlinea
     */
    public function setEstaciones($estaciones)
    {
        $this->estaciones = $estaciones;

        return $this;
    }

    /**
     * Get estaciones
     *
     * @return int
     */
    public function getEstaciones()
    {
        return $this->estaciones;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return configuracionlinea
     */
    public function setVersion(\GestionBundle\Entity\version $version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }


    /**
     * Set idAsignarproceso
     *
     * @param \GestionBundle\Entity\asignarproceso $idAsignarproceso
     *
     * @return configuracionlinea
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
     * Set estado
     *
     * @param string $estado
     *
     * @return configuracionlinea
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
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return configuracionlinea
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
     * @return configuracionlinea
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
     * Set tackTeorico
     *
     * @param integer $tackTeorico
     *
     * @return configuracionlinea
     */
    public function setTackTeorico($tackTeorico)
    {
        $this->tackTeorico = $tackTeorico;

        return $this;
    }

    /**
     * Get tackTeorico
     *
     * @return integer
     */
    public function getTackTeorico()
    {
        return $this->tackTeorico;
    }

    /**
     * Set eqSemana
     *
     * @param string $eqSemana
     *
     * @return configuracionlinea
     */
    public function setEqSemana($eqSemana)
    {
        $this->eqSemana = $eqSemana;

        return $this;
    }

    /**
     * Get eqSemana
     *
     * @return string
     */
    public function getEqSemana()
    {
        return $this->eqSemana;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return configuracionlinea
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
}
