<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
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
	 * @ORM\Column(type="string", length=64, name="qr_code", unique=true)
	 * 
	 * @Groups("group-all")
	 */
	private $qrCode;
    
    /**
     * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
     */
    private $surname;
    
    /**
     * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
     */
    private $office;

    /**
     * @ORM\Column(type="integer")
	 * 
	 * @Groups("group-all")
     */
    private $floor;
    
	public function __construct($name, $surname, $office, $floor, $qrCode)
	{
		$this->setName($name);
		$this->setSurname($surname);
		$this->setOffice($office);
		$this->setFloor($floor);
		//$this->setQrCode($this->generateRandomString(8));
		$this->setQrCode($qrCode);
	}
	
	public static function getRandomQrCode()
	{
		return User::generateRandomString(8);
	}
	
	static function generateRandomString($length = 8) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	public function getId(): ?int
    {
        return $this->id;
    }
	
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
