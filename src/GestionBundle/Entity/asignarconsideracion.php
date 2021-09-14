<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * asignarconsideracion
 *
 * @ORM\Table(name="asignarconsideracion")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\asignarconsideracionRepository")
 */
class asignarconsideracion
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
     * @ORM\JoinColumn(name="id_operacionbasica", referencedColumnName="id")
     */
    private $idOperacionbasica;

    /**
     * @ORM\ManyToOne(targetEntity="conscalidad")
     * @ORM\JoinColumn(name="id_consideracioncalidad", referencedColumnName="id")
     */
    private $idConsideracioncalidad;


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
     * Set idOperacionbasica
     *
     * @param \GestionBundle\Entity\opbasica $idOperacionbasica
     *
     * @return asignarconsideracion
     */
    public function setIdOperacionbasica(\GestionBundle\Entity\opbasica $idOperacionbasica = null)
    {
        $this->idOperacionbasica = $idOperacionbasica;

        return $this;
    }

    /**
     * Get idOperacionbasica
     *
     * @return \GestionBundle\Entity\opbasica
     */
    public function getIdOperacionbasica()
    {
        return $this->idOperacionbasica;
    }

    /**
     * Set idConsideracioncalidad
     *
     * @param \GestionBundle\Entity\conscalidad $idConsideracioncalidad
     *
     * @return asignarconsideracion
     */
    public function setIdConsideracioncalidad(\GestionBundle\Entity\conscalidad $idConsideracioncalidad = null)
    {
        $this->idConsideracioncalidad = $idConsideracioncalidad;

        return $this;
    }

    /**
     * Get idConsideracioncalidad
     *
     * @return \GestionBundle\Entity\conscalidad
     */
    public function getIdConsideracioncalidad()
    {
        return $this->idConsideracioncalidad;
    }
}
