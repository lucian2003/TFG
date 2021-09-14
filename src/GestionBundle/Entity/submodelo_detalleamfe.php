<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * submodelo_detalleamfe
 *
 * @ORM\Table(name="submodelo_detalleamfe")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\submodelo_detalleamfeRepository")
 */
class submodelo_detalleamfe
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
     * @var int
     *
     * @ORM\Column(name="id_submodelo", type="integer")
     */
    private $idSubmodelo;

    /**
     * @var int
     *
     * @ORM\Column(name="id_detalleamfe", type="integer")
     */
    private $idDetalleamfe;

    /**
     * @var string
     *
     * @ORM\Column(name="responsable", type="string", length=255, nullable=true)
     */
    private $responsable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_estimada", type="date")
     */
    private $fechaEstimada;

    /**
     * @var string
     *
     * @ORM\Column(name="realizada", type="string", length=255, nullable=true)
     */
    private $realizada;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_realizado", type="date", nullable=true)
     */
    private $fechaRealizado;


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
     * Set idSubmodelo
     *
     * @param integer $idSubmodelo
     *
     * @return submodelo_detalleamfe
     */
    public function setIdSubmodelo($idSubmodelo)
    {
        $this->idSubmodelo = $idSubmodelo;

        return $this;
    }

    /**
     * Get idSubmodelo
     *
     * @return integer
     */
    public function getIdSubmodelo()
    {
        return $this->idSubmodelo;
    }

    /**
     * Set idDetalleamfe
     *
     * @param integer $idDetalleamfe
     *
     * @return submodelo_detalleamfe
     */
    public function setIdDetalleamfe($idDetalleamfe)
    {
        $this->idDetalleamfe = $idDetalleamfe;

        return $this;
    }

    /**
     * Get idDetalleamfe
     *
     * @return integer
     */
    public function getIdDetalleamfe()
    {
        return $this->idDetalleamfe;
    }

    /**
     * Set responsable
     *
     * @param string $responsable
     *
     * @return submodelo_detalleamfe
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return string
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set fechaEstimada
     *
     * @param \DateTime $fechaEstimada
     *
     * @return submodelo_detalleamfe
     */
    public function setFechaEstimada($fechaEstimada)
    {
        $this->fechaEstimada = $fechaEstimada;

        return $this;
    }

    /**
     * Get fechaEstimada
     *
     * @return \DateTime
     */
    public function getFechaEstimada()
    {
        return $this->fechaEstimada;
    }

    /**
     * Set realizada
     *
     * @param string $realizada
     *
     * @return submodelo_detalleamfe
     */
    public function setRealizada($realizada)
    {
        $this->realizada = $realizada;

        return $this;
    }

    /**
     * Get realizada
     *
     * @return string
     */
    public function getRealizada()
    {
        return $this->realizada;
    }

    /**
     * Set fechaRealizado
     *
     * @param \DateTime $fechaRealizado
     *
     * @return submodelo_detalleamfe
     */
    public function setFechaRealizado($fechaRealizado)
    {
        $this->fechaRealizado = $fechaRealizado;

        return $this;
    }

    /**
     * Get fechaRealizado
     *
     * @return \DateTime
     */
    public function getFechaRealizado()
    {
        return $this->fechaRealizado;
    }
}
