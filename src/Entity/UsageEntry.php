<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Swagger\Annotations AS SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsageEntryRepository")
 * @ORM\Table(name="usage_history")
 * 
 * @SWG\Definition()
 */
class UsageEntry
{
    /**
     * @ORM\Id()
	 * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Entry ID in the database."
	 * )
     */
    private $id;

    /**
	 * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="User associated with this entry."
	 * )
     */
    private $user;

    /**
	 * @ORM\ManyToOne(targetEntity="Device")
     * @ORM\JoinColumn(name="device", referencedColumnName="id")
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     description="Device associated with this entry."
	 * )
     */
    private $device;

    /**
     * @ORM\Column(type="datetime")
	 * 
	 * @Groups("group-all")
	 * 
	 * @SWG\Property(
	 *     type="datetime",
	 *     description="When user took device."
	 * )
     */
    private $timestamp;

    public function __construct(User $user, Device $device, \DateTime $timestamp)
	{
		$this->setUser($user);
		$this->setDevice($device);
		$this->setTimestamp($timestamp);
	}

	public function getId(): ?int
    {
        return $this->id;
    }

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
