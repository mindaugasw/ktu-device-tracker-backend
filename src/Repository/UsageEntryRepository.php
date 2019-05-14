<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\UsageEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UsageEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsageEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsageEntry[]    findAll()
 * @method UsageEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsageEntryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UsageEntry::class);
    }

	/**
	 * Returns usage history in pages.
	 * 1st page - newest items,
	 * Last page - oldest items.
	 * 
	 * @param int $page page to get
	 * @param int $perPage max entries per 1 page
	 * @param int $totalCount total number of entries in the table
	 * @return UsageEntry[] An array of UsageEntry objects
	 */
	public function findPaginatedUsageHistory_all(int $page, int $perPage, int $totalCount) 
	{
		// Page numbers converting so that newest items would be on the 1st page
		$firstEntry = $totalCount - $page * $perPage;
		$maxResults = $firstEntry < 0 ? $perPage + $firstEntry : $perPage;
			
		if ($firstEntry < 0)
			$firstEntry = 0;
		
		if ($page > ceil($totalCount / $perPage))
			$maxResults = 0;
				
		// First result = 0 or 1?
		return $this->createQueryBuilder('u')
			->setFirstResult($firstEntry)
			->setMaxResults($maxResults)
			->getQuery()
			->getResult();
	}

	/**
	 * Returns single device usage history in pages.
	 * 1st page - newest items,
	 * Last page - oldest items.
	 *
	 * @param int $page page to get
	 * @param int $perPage max entries per 1 page
	 * @param int $totalCount total number of entries in the table
	 * @return UsageEntry[] An array of UsageEntry objects
	 */
	public function findPaginatedUsageHistory_single(Device $device, int $page, int $perPage, int $totalCount)
	{
		// Page numbers converting so that newest items would be on the 1st page
		$firstEntry = $totalCount - $page * $perPage;
		$maxResults = $firstEntry < 0 ? $perPage + $firstEntry : $perPage;

		if ($firstEntry < 0)
			$firstEntry = 0;

		if ($page > ceil($totalCount / $perPage))
			$maxResults = 0;

		// First result = 0 or 1?
		return $this->createQueryBuilder('u')
			->where('u.device = :deviceId')
			->setFirstResult($firstEntry)
			->setMaxResults($maxResults)
			->setParameter('deviceId', $device->getId())
			->getQuery()
			->getResult();
	}
    

    // /**
    //  * @return UsageEntry[] Returns an array of UsageEntry objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsageEntry
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
