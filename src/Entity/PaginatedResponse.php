<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Swagger\Annotations AS SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @SWG\Definition()
 */
class PaginatedResponse
{
	/**
	 * @var int
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Returned page number."
	 * )
	 */
	private $page;

	/**
	 * @var int
	 *
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Maximum number of items per page."
	 * )
	 */
	private $perPage;
	
	/**
	 * @var int
	 *
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Number of pages."
	 * )
	 */
	private $pagesCount;

	/**
	 * @var int
	 *
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Number of returned items in this page."
	 * )
	 */
	private $itemsCount;

	/**
	 * @var int
	 *
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Total number of items in the database."
	 * )
	 */
	private $totalCount;

	/**
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     type="array",
	 * 	   @SWG\Items(ref=@Model(type=UsageEntry::class))
	 * )
	 */
	private $items;

	/**
	 * PaginatedResponse constructor.
	 * @param int $page
	 * @param int $perPage
	 * @param int $pagesCount
	 * @param int $itemsCount
	 * @param $items
	 */
	public function __construct(int $page, int $perPage, int $pagesCount, int $itemsCount, int $totalCount, $items)
	{
		$this->page = $page;
		$this->perPage = $perPage;
		$this->pagesCount = $pagesCount;
		$this->itemsCount = $itemsCount;
		$this->totalCount = $totalCount;
		$this->items = $items;
	}


	/**
	 * @return int
	 */
	public function getPage(): int
	{
		return $this->page;
	}

	/**
	 * @param int $page
	 */
	public function setPage(int $page): void
	{
		$this->page = $page;
	}

	/**
	 * @return int
	 */
	public function getPerPage(): int
	{
		return $this->perPage;
	}

	/**
	 * @param int $perPage
	 */
	public function setPerPage(int $perPage): void
	{
		$this->perPage = $perPage;
	}

	/**
	 * @return int
	 */
	public function getPagesCount(): int
	{
		return $this->pagesCount;
	}

	/**
	 * @param int $pagesCount
	 */
	public function setPagesCount(int $pagesCount): void
	{
		$this->pagesCount = $pagesCount;
	}

	/**
	 * @return int
	 */
	public function getItemsCount(): int
	{
		return $this->itemsCount;
	}

	/**
	 * @param int $itemsCount
	 */
	public function setItemsCount(int $itemsCount): void
	{
		$this->itemsCount = $itemsCount;
	}

	/**
	 * @return int
	 */
	public function getTotalCount(): int
	{
		return $this->totalCount;
	}

	/**
	 * @param int $totalCount
	 */
	public function setTotalCount(int $totalCount): void
	{
		$this->totalCount = $totalCount;
	}
	
	/**
	 * @return mixed
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @param mixed $items
	 */
	public function setItems($items): void
	{
		$this->items = $items;
	}
}