<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * detalleconfiguracion
 *
 * @ORM\Table(name="detalleconfiguracion")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\detalleconfiguracionRepository")
 */
class detalleconfiguracion
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
     * @ORM\ManyToOne(targetEntity="configuracionlinea")
     * @ORM\JoinColumn(name="id_configuracionlinea", referencedColumnName="id")
     */
    private $idConfiguracionlinea;

    /**
     * @ORM\ManyToOne(targetEntity="tareaAsignada")
     * @ORM\JoinColumn(name="id_tareaAsignada", referencedColumnName="id")
     */
    private $idTareaAsignada;

    /**
     * @var int
     *
     * @ORM\Column(name="estacion", type="integer")
     */
    private $estacion = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="operario", type="integer")
     */
    private $operario = '0';

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set estacion
     *
     * @param integer $estacion
     *
     * @return detalleconfiguracion
     */
    public function setEstacion($estacion)
    {
        $this->estacion = $estacion;

        return $this;
    }

    /**
     * Get estacion
     *
     * @return int
     */
    public function getEstacion()
    {
        return $this->estacion;
    }

    /**
     * Set operario
     *
     * @param integer $operario
     *
     * @return detalleconfiguracion
     */
    public function setOperario($operario)
    {
        $this->operario = $operario;

        return $this;
    }

    /**
     * Get operario
     *
     * @return int
     */
    public function getOperario()
    {
        return $this->operario;
    }


    /**
     * Set position
     *
     * @param integer $position
     *
     * @return detalleconfiguracion
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

    /**
     * Set idConfiguracionlinea
     *
     * @param \GestionBundle\Entity\configuracionlinea $idConfiguracionlinea
     *
     * @return detalleconfiguracion
     */
    public function setIdConfiguracionlinea(\GestionBundle\Entity\configuracionlinea $idConfiguracionlinea = null)
    {
        $this->idConfiguracionlinea = $idConfiguracionlinea;

        return $this;
    }

    /**
     * Get idConfiguracionlinea
     *
     * @return \GestionBundle\Entity\configuracionlinea
     */
    public function getIdConfiguracionlinea()
    {
        return $this->idConfiguracionlinea;
    }

    /**
     * Set idTareaAsignada
     *
     * @param \GestionBundle\Entity\tareaAsignada $idTareaAsignada
     *
     * @return detalleconfiguracion
     */
    public function setIdTareaAsignada(\GestionBundle\Entity\tareaAsignada $idTareaAsignada = null)
    {
        $this->idTareaAsignada = $idTareaAsignada;

        return $this;
    }

    /**
     * Get idTareaAsignada
     *
     * @return \GestionBundle\Entity\tareaAsignada
     */
    public function getIdTareaAsignada()
    {
        return $this->idTareaAsignada;
    }

    /**
     * Set tiempo
     *
     * @param integer $tiempo
     *
     * @return detalleconfiguracion
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
}
