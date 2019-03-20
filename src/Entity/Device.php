<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device 
{
    /*
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    //private $id;
	
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="string", length=64, name="unique_id", unique=true)
	 */
    private $uniqueId;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
    private $name;
    
	/**
	 * LastUser value could be also retrieved from UsageHistory, but decided to put it here for performance reasons,
	 * as otherwise it would be needed to go through UsageHistory and search LastUser for every single device
	 * everytime device list is displayed.
	 * 
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="last_user", referencedColumnName="qr_code")
	 */
    private $lastUser;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $simCard;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $os;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $enabled;
	
	
    /*public function getId(): ?int
    {
        return $this->id;
    }*/
    
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
	
	public function setLastUser(User $user): self 
	{
		$this->lastUser = $user;
			
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

}
