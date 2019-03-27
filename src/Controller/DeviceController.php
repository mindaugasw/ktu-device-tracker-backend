<?php

namespace App\Controller;

use App\Entity\Device;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
	 * @Route("/device/all", name="device_list")
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
	
	/**
	 * @Route("device/update", name="device_update")
	 */
	public function updateDevice(Request $request)
	{
		$uniqueId = $request->request->get('uniqueId');
		if (!isset($uniqueId))
			return new Response(null, Response::HTTP_BAD_REQUEST);
		
		$em = $this->getDoctrine()->getManager();
		$device = $em->getRepository(Device::class)->findOneBy(['uniqueId' => $uniqueId]);
		
		if (!isset($device))
			return new Response(null, Response::HTTP_NOT_FOUND);
		
		$name = $request->request->get('name');
		$simCard = $request->request->get('simCard');
		$os = $request->request->get('os');
		$enabled = $request->request->get('enabled');
		
		if (isset($name)) 	 	$device->setName($name);
		if (isset($simCard))	$device->setSimCard(filter_var($simCard, FILTER_VALIDATE_BOOLEAN));
		if (isset($os))			$device->setOs($os);
		if (isset($enabled))
			$device->setEnabled(filter_var($enabled, FILTER_VALIDATE_BOOLEAN)); // filter_var = bool parse from string
		
		
		$em->flush();
		
		// NOT WORKING: lastActivity is missing in result json. There's some difficulties serializing DateTime type.
		$json = $this->serializer->serialize($device, 'json', ['groups' => 'group-all']);		
		return new Response($json, Response::HTTP_OK, ['content-type' => 'application/json']);
	}
	
	/**
	 * @Route("/device/new", name="device_new")
	 */
	public function createNewDevice(Request $request)
	{
		$uniqueId = $request->request->get('uniqueId');
		$name = $request->request->get('name');
		$simCard = $request->request->get('simCard');
		$os = $request->request->get('os');
		$enabled = true; // for now, defaults to true. Should require admin verification later.
		
		if (!isset($uniqueId, $name, $simCard, $os, $enabled))
			return new Response(null, Response::HTTP_BAD_REQUEST);
		
		$device = new Device($uniqueId, $name, $simCard, $os, $enabled);
		
		// BAD_REQUEST if there already is a device with same uniqueId
		$oldDevice = $this->getDoctrine()->getRepository(Device::class)->findOneBy(['uniqueId' => $uniqueId]);
		if (isset($oldDevice))
			return new Response(null, Response::HTTP_BAD_REQUEST);
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($device);
		$em->flush();
		
		$json = $this->serializer->serialize($device, 'json', ['groups' => 'group-all']);
		return new Response($json, Response::HTTP_OK, ['content-type' => 'application/json']);
	}
	
	/**
	 * @Route("/device/delete", name="device_delete")
	 */
	public function deleteDevice(Request $request)
	{
		$uniqueId = $request->request->get('uniqueId');
		if (!isset($uniqueId))
			return new Response(null, Response::HTTP_BAD_REQUEST);
		
		$device = $this->getDoctrine()->getRepository(Device::class)->findOneBy(['uniqueId' => $uniqueId]);
		if (!isset($device))
			return new Response(null, Response::HTTP_NOT_FOUND);
		
		$em = $this->getDoctrine()->getManager();
		$em->remove($device);
		$em->flush();
		
		return new Response(null, Response::HTTP_OK);		
	}
	
	/**
	 * Must be at the bottom of the controller as otherwise it also catches all other routes.
	 *
	 * @Route("device/{deviceId}", name="device_single")
	 */
	public function getDeviceSingle(string $deviceId)
	{
		$device = $this->getDoctrine()
			->getRepository(Device::class)
			->findOneBy(['uniqueId' => $deviceId]);
		
		$json = $this->serializer->serialize(
			$device,
			'json', ['groups' => 'group-all']
		);
		
		return new Response(
			$json,
			Response::HTTP_OK,
			['content-type' => 'application/json']
		);
	}
}