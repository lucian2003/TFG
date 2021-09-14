<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tarea_amfe
 *
 * @ORM\Table(name="tarea_amfe")
 * @ORM\Entity(repositoryClass="GestionBundle\Repository\tarea_amfeRepository")
 */
class tarea_amfe
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
     * @ORM\ManyToOne(targetEntity="amfe")
     * @ORM\JoinColumn(name="id_amfe", referencedColumnName="id")
     */
    private $idAmfe;


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
     * Set idAmfe
     *
     * @param \GestionBundle\Entity\amfe $idAmfe
     *
     * @return tarea_amfe
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

    /**
     * Set idTareaAsignada
     *
     * @param \GestionBundle\Entity\tareaAsignada $idTareaAsignada
     *
     * @return tarea_amfe
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
}
