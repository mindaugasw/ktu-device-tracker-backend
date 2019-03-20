<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsageEntryRepository")
 * @ORM\Table(name="usage_history")
 */
class UsageEntry
{
    /*
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    //private $id;

    /**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="qr_code")
     */
    private $user;

    /**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="Device")
     * @ORM\JoinColumn(name="device", referencedColumnName="unique_id")
     */
    private $device;

    /**
	 * @ORM\Id()
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /*public function getId(): ?int
    {
        return $this->id;
    }*/

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(Device $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
