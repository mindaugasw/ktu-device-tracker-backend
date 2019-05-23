<?php

namespace App\Entity;

use App\Utils\RandomString;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Swagger\Annotations AS SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * 
 * @SWG\Definition()
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="User ID in the database."
	 * )
     */
    private $id;
	
	/**
	 * @ORM\Column(type="string", length=64, name="qr_code", unique=true)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="User's QR code."
	 * )
	 */
	private $qrCode;
    
    /**
     * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="User's name."
	 * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="User's surname."
	 * )
     */
    private $surname;
    
    /**
     * @ORM\Column(type="string", length=255)
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Office that this user works in.",
	 *     example="Vilnius"
	 * )
     */
    private $office;

    /*
     * @ORM\Column(type="integer")
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Floor that this user works at."
	 * )
     */
    //private $floor;
    
	public function __construct($name, $surname, $office, $qrCode)
	{
		$this->setName($name);
		$this->setSurname($surname);
		$this->setOffice($office);
		//$this->setFloor($floor);
		//$this->setQrCode($this->generateRandomString(8));
		$this->setQrCode($qrCode);
	}
	
	public static function getRandomQrCode()
	{
		return RandomString::generateRandomString(8);
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

    /*public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }*/
	
}
