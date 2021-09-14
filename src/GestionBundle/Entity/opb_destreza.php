<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * opb_destreza
 *
 * @ORM\Table(name="opb_destreza")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\opb_destrezaRepository")
 */
class opb_destreza
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
     * @ORM\ManyToOne(targetEntity="opbasica")
     * @ORM\JoinColumn(name="id_opbasica", referencedColumnName="id")
     */
    private $idOpbasica;

    /**
     * @ORM\ManyToOne(targetEntity="destreza")
     * @ORM\JoinColumn(name="id_destreza", referencedColumnName="id")
     */
    private $idDestreza;


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
     * Set idOpbasica
     *
     * @param \GestionBundle\Entity\opbasica $idOpbasica
     *
     * @return opb_destreza
     */
    public function setIdOpbasica(\GestionBundle\Entity\opbasica $idOpbasica = null)
    {
        $this->idOpbasica = $idOpbasica;

        return $this;
    }

    /**
     * Get idOpbasica
     *
     * @return \GestionBundle\Entity\opbasica
     */
    public function getIdOpbasica()
    {
        return $this->idOpbasica;
    }

    /**
     * Set idDestreza
     *
     * @param \GestionBundle\Entity\destreza $idDestreza
     *
     * @return opb_destreza
     */
    public function setIdDestreza(\GestionBundle\Entity\destreza $idDestreza = null)
    {
        $this->idDestreza = $idDestreza;

        return $this;
    }

    /**
     * Get idDestreza
     *
     * @return \GestionBundle\Entity\destreza
     */
    public function getIdDestreza()
    {
        return $this->idDestreza;
    }
}
