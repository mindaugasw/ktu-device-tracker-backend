<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device
{
	const OS_ANDROID = 1;
	const OS_IOS = 2;
	
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
    private $name;
    
    //private $last user;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $simCard;
	
	/**
	 * @ORM\Column(type="smallint")
	 */
	private $os;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $osVersion;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $enabled;
	
	
    public function getId(): ?int
    {
        return $this->id;
    }
}
