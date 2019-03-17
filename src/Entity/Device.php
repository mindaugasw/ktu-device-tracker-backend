<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    private $name;
    //private $last user;
	private $simCard;
	private $os;
	private $osVersion;
	private $enabled;
	
    public function getId(): ?int
    {
        return $this->id;
    }
}
