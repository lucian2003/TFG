<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * detalleamfe
 *
 * @ORM\Table(name="detalleamfe")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\detalleamfeRepository")
 */
class detalleamfe
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
     * @ORM\ManyToOne(targetEntity="amfe")
     * @ORM\JoinColumn(name="id_amfe", referencedColumnName="id")
     */
    private $idAmfe;

    /**
     * @var string
     *
     * @ORM\Column(name="modo_fallo", type="string", length=255)
     */
    private $modoFallo;

    /**
     * @var int
     *
     * @ORM\Column(name="gravedad", type="integer")
     */
    private $gravedad;

    /**
     * @var int
     *
     * @ORM\Column(name="frecuencia", type="integer")
     */
    private $frecuencia;

    /**
     * @var int
     *
     * @ORM\Column(name="deteccion", type="integer")
     */
    private $deteccion;

    /**
     * @var int
     *
     * @ORM\Column(name="npr", type="integer")
     */
    private $npr;

    /**
     * @var string
     *
     * @ORM\Column(name="accion", type="string", length=255, nullable=true)
     */
    private $accion;

    /**
     * @var int
     *
     * @ORM\Column(name="gravedad_t", type="integer", nullable=true)
     */
    private $gravedadT;

    /**
     * @var int
     *
     * @ORM\Column(name="frecuencia_t", type="integer", nullable=true)
     */
    private $frecuenciaT;

    /**
     * @var int
     *
     * @ORM\Column(name="deteccion_t", type="integer", nullable=true)
     */
    private $deteccionT;

    /**
     * @var int
     *
     * @ORM\Column(name="nuevo_npr", type="integer", nullable=true)
     */
    private $nuevoNpr;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set modoFallo
     *
     * @param string $modoFallo
     *
     * @return detalleamfe
     */
    public function setModoFallo($modoFallo)
    {
        $this->modoFallo = $modoFallo;

        return $this;
    }

    /**
     * Get modoFallo
     *
     * @return string
     */
    public function getModoFallo()
    {
        return $this->modoFallo;
    }

    /**
     * Set gravedad
     *
     * @param integer $gravedad
     *
     * @return detalleamfe
     */
    public function setGravedad($gravedad)
    {
        $this->gravedad = $gravedad;

        return $this;
    }

    /**
     * Get gravedad
     *
     * @return integer
     */
    public function getGravedad()
    {
        return $this->gravedad;
    }

    /**
     * Set frecuencia
     *
     * @param integer $frecuencia
     *
     * @return detalleamfe
     */
    public function setFrecuencia($frecuencia)
    {
        $this->frecuencia = $frecuencia;

        return $this;
    }

    /**
     * Get frecuencia
     *
     * @return integer
     */
    public function getFrecuencia()
    {
        return $this->frecuencia;
    }

    /**
     * Set deteccion
     *
     * @param integer $deteccion
     *
     * @return detalleamfe
     */
    public function setDeteccion($deteccion)
    {
        $this->deteccion = $deteccion;

        return $this;
    }

    /**
     * Get deteccion
     *
     * @return integer
     */
    public function getDeteccion()
    {
        return $this->deteccion;
    }

    /**
     * Set npr
     *
     * @param integer $npr
     *
     * @return detalleamfe
     */
    public function setNpr($npr)
    {
        $this->npr = $npr;

        return $this;
    }

    /**
     * Get npr
     *
     * @return integer
     */
    public function getNpr()
    {
        return $this->npr;
    }

    /**
     * Set accion
     *
     * @param string $accion
     *
     * @return detalleamfe
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;

        return $this;
    }

    /**
     * Get accion
     *
     * @return string
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set gravedadT
     *
     * @param integer $gravedadT
     *
     * @return detalleamfe
     */
    public function setGravedadT($gravedadT)
    {
        $this->gravedadT = $gravedadT;

        return $this;
    }

    /**
     * Get gravedadT
     *
     * @return integer
     */
    public function getGravedadT()
    {
        return $this->gravedadT;
    }

    /**
     * Set frecuenciaT
     *
     * @param integer $frecuenciaT
     *
     * @return detalleamfe
     */
    public function setFrecuenciaT($frecuenciaT)
    {
        $this->frecuenciaT = $frecuenciaT;

        return $this;
    }

    /**
     * Get frecuenciaT
     *
     * @return integer
     */
    public function getFrecuenciaT()
    {
        return $this->frecuenciaT;
    }

    /**
     * Set deteccionT
     *
     * @param integer $deteccionT
     *
     * @return detalleamfe
     */
    public function setDeteccionT($deteccionT)
    {
        $this->deteccionT = $deteccionT;

        return $this;
    }

    /**
     * Get deteccionT
     *
     * @return integer
     */
    public function getDeteccionT()
    {
        return $this->deteccionT;
    }

    /**
     * Set nuevoNpr
     *
     * @param integer $nuevoNpr
     *
     * @return detalleamfe
     */
    public function setNuevoNpr($nuevoNpr)
    {
        $this->nuevoNpr = $nuevoNpr;

        return $this;
    }

    /**
     * Get nuevoNpr
     *
     * @return integer
     */
    public function getNuevoNpr()
    {
        return $this->nuevoNpr;
    }

    /**
     * Set idAmfe
     *
     * @param \GestionBundle\Entity\amfe $idAmfe
     *
     * @return detalleamfe
     */
    public function setIdAmfe(\GestionBundle\Entity\amfe $idAmfe = null)
    {
        $this->idAmfe = $idAmfe;

        return $this;
    }

    /**
     * Get idAmfe
     *
     * @return \GestionBundle\Entity\amfe
     */
    public function getIdAmfe()
    {
        return $this->idAmfe;
    }
}
