<?php

namespace GestionBundle\Repository;
use \Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * modeloRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class modeloRepository extends \Doctrine\ORM\EntityRepository
{
	/*public function getPaginateModelos($pageSize=5, $currentPage=1) {

		$em = $this->getEntityManager();

		$dql = "SELECT e FROM GestionBundle\Entity\modelo e WHERE e.active = 1 ORDER BY e.id DESC";

		$query = $em->createQuery($dql)
				->setFirstResult($pageSize*($currentPage-1))
				->setMaxResults($pageSize);

		$paginator = new Paginator($query, $fetchJoinCollection = true);
		return $paginator;

	}*/

	
}