<?php

namespace App\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /*
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    //private $id;
	
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="string", length=64, name="qr_code", unique=true)
	 */
	private $qrCode;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $office;

    /**
     * @ORM\Column(type="integer")
     */
    private $floor;

    /*public function getId(): ?int
    {
        return $this->id;
    }*/
	
	public function getQrCode(): ?string
	{
		return $this->qrCode;
	}
	
	public function setQrCode(string $qrCode): self
	{
		$this->qrCode = $qrCode;
		
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }
    
    public function getOffice(): ?string
    {
        return $this->office;
    }

    public function setOffice(string $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }
	
}
