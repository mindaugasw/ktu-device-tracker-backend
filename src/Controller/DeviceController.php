<?php

namespace App\Controller;

use App\Entity\Device;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class DeviceController extends AbstractController
{
	private $serializer;
	
	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}
	
	/**
	 * @Route("/devices", name="device_list")
	 */
	public function getDeviceList()
	{
		$devices = $this->getDoctrine()
			->getRepository(Device::class)
			->findAll();
		
		$json = $this->serializer->serialize(
			$devices,
			'json', ['groups' => 'group-all']
		);
		
		return new Response(
			$json,
			Response::HTTP_OK,
			['content-type' => 'application/json']
		);
	}
	
	/*
	 * @Route("device/{device}", name="device_single"
	 */
	/*public function getDeviceSingle(SerializerInterface $serializer, Device $device)
	{
		
	}*/
	
	/*
	 * @Route("/device/new", name="device_new")
	 */
	/*public function createNewDevice()
	{
		
	}*/
}