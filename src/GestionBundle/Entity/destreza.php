<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * destreza
 *
 * @ORM\Table(name="destreza")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\destrezaRepository")
 */
class destreza
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="objetivo", type="string", length=255)
     */
    private $objetivo;

    /**
     * @ORM\ManyToOne(targetEntity="tipo_destreza")
     * @ORM\JoinColumn(name="id_tipodestreza", referencedColumnName="id")
     */
    private $idTipodestreza;


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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return destreza
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

    /**
     * Set objetivo
     *
     * @param string $objetivo
     *
     * @return destreza
     */
    public function setObjetivo($objetivo)
    {
        $this->objetivo = $objetivo;

        return $this;
    }

    /**
     * Get objetivo
     *
     * @return string
     */
    public function getObjetivo()
    {
        return $this->objetivo;
    }


    /**
     * Set idTipodestreza
     *
     * @param \GestionBundle\Entity\tipo_destreza $idTipodestreza
     *
     * @return destreza
     */
    public function setIdTipodestreza(\GestionBundle\Entity\tipo_destreza $idTipodestreza = null)
    {
        $this->idTipodestreza = $idTipodestreza;

        return $this;
    }

    /**
     * Get idTipodestreza
     *
     * @return \GestionBundle\Entity\tipo_destreza
     */
    public function getIdTipodestreza()
    {
        return $this->idTipodestreza;
    }
}
