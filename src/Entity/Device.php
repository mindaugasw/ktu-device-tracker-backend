<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Swagger\Annotations AS SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 * 
 * @SWG\Definition()
 */
class Device 
{
	
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Device ID in the database."
	 * )
     */
    private $id;
	
	/**
	 * @ORM\Column(type="string", length=64, name="unique_id", unique=true)
	 * 
	 * @Groups("group-all")
	 *
	 * @SWG\Property(
	 *     description="Unique device identifier.",
	 * )
	 */
    private $uniqueId;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Device name."
	 * )
	 */
    private $name;
    
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="last_user", referencedColumnName="id", nullable=true)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Last user that took this device."
	 * )
	 * 
	 * LastUser value could be also retrieved from UsageHistory, but decided to put it here as well for performance reasons.
	 */
    private $lastUser;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     type="datetime",
	 *     description="Last time someone used this device."
	 * )
	 */
    private $lastActivity;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Is there SIM card in the device?"
	 * )
	 */
	private $simCard;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     example="Android 9.1"
	 * )
	 */
	private $os;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 * 
	 * @SWG\Property(
	 *     description="Currently not used.
	           Is device enabled in the system? If not, won't be shown in devices list."
	 * )
	 */
	private $enabled;
	
	
	public function __construct($uniqueId, $name, $os)
	{
		$this->uniqueId = $uniqueId;
		$this->name = $name;
		//$this->simCard = $simCard;
		$this->os = $os;
		//$this->enabled = $enabled;
		$this->setLastActivity(new \DateTime('now'));
	}
	
	
	public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getUniqueId(): ?string 
	{
		return $this->uniqueId;
	}
	
	public function setUniqueId(string $uniqueId): self 
	{
		$this->uniqueId = $uniqueId;
		
		return $this;
	}
    
    public function getName(): ?string
	{
		return $this->name;
	}
	
	public function setName(string $name): self
	{
		$this->name = $name;
		
		return $this;
	}
	
	public function getLastUser(): ?User
	{
		return $this->lastUser;
	}
	
	public function setLastUser(?User $user): self 
	{
		$this->lastUser = $user;
			
		return $this;
	}
	
	public function getLastActivity(): ?\DateTime
	{
		return $this->lastActivity;
	}
	
	public function setLastActivity(\DateTime $dateTime): self 
	{
		$this->lastActivity = $dateTime;
		
		return $this;
	}
	
	public function getSimCard(): ?bool 
	{
		return $this->simCard;
	}
	
	public function setSimCard(bool $simCard): self
	{
		$this->simCard = $simCard;
			
		return $this;
	}
	
	public function getOs(): ?string 
	{
		return $this->os;
	}
	
	public function setOs(string $os): self
	{
		$this->os = $os;
			
		return $this;
	}
	
	public function isEnabled(): bool 
	{
		return $this->enabled;
	}
	
	public function setEnabled(bool $enabled): self 
	{
		$this->enabled = $enabled;
		
		return $this;
	}

}
