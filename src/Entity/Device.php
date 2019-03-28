<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device 
{
	
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups("group-all")
     */
    private $id;
	
	/**
	 * @ORM\Column(type="string", length=64, name="unique_id", unique=true)
	 * 
	 * @Groups("group-all")
	 */
    private $uniqueId;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
	 */
    private $name;
    
	/**
	 * LastUser value could be also retrieved from UsageHistory, but decided to put it here for performance reasons,
	 * as otherwise it would be needed to go through UsageHistory and search LastUser for every single device
	 * everytime device list is displayed.
	 * 
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="last_user", referencedColumnName="id", nullable=true)
	 * 
	 * @Groups("group-all")
	 */
    private $lastUser;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * 
	 * @Groups("group-all")
	 */
    private $lastActivity;
	
	/**
	 * @ORM\Column(type="boolean")
	 * 
	 * @Groups("group-all")
	 */
	private $simCard;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
	 */
	private $os;
	
	/**
	 * @ORM\Column(type="boolean")
	 * 
	 * @Groups("group-all")
	 */
	private $enabled;
	
	
	public function __construct($uniqueId, $name, $simCard, $os, $enabled)
	{
		$this->uniqueId = $uniqueId;
		$this->name = $name;
		$this->simCard = $simCard;
		$this->os = $os;
		$this->enabled = $enabled;
		$this->lastActivity = new \DateTime('now');
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
