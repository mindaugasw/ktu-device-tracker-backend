<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\UsageEntry;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class DeviceController extends AbstractController
{
	private $serializer;
	
	private $headers = ['content-type' => 'application/json', 'Access-Control-Allow-Origin' => '*'];
	
	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}
	
	/**
	 * @Route("/device/all", name="device_list")
	 */
	public function getDevicesList()
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
			$this->headers
		);
	}

	/**
	 * @Route("/device/all/history", name="device_all_history")
	 */
	public function getAllDevicesHistory()
	{
		$history = $this->getDoctrine()->getRepository(UsageEntry::class)->findAll();		
		$json = $this->serializer->serialize(
			$history, 
			'json', ['groups' => 'group-all']
		);
		
		return new Response(
			$json, 
			Response::HTTP_OK,
			$this->headers
		);		
	}

	/**
	 * @Route("/device/{deviceId}/history", name="device_single_history")
	 */
	public function getSingleDeviceHistory(string $deviceId)
	{
		$device = $this->getDoctrine()->getRepository(Device::class)->findBy(['uniqueId' => $deviceId]);
		if (sizeof($device) === 0)
			return new Response(null, Response::HTTP_NOT_FOUND, $this->headers);
		
		$history = $this->getDoctrine()->getRepository(UsageEntry::class)->findBy(['device' => $device]);
		
		$json = $this->serializer->serialize(
			$history,
			'json', ['groups' => 'group-all']
		);
		
		return new Response(
			$json,
			Response::HTTP_OK,
			$this->headers
		);
	}
	
	/**
	 * @Route("device/update", name="device_update")
	 */
	public function updateDevice(Request $request)
	{
		$uniqueId = $request->request->get('uniqueId');
		$name = $request->request->get('name');
		$simCard = $request->request->get('simCard');
		$os = $request->request->get('os');
		//$enabled = $request->request->get('enabled');
		
		if (!isset($uniqueId))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		$em = $this->getDoctrine()->getManager();
		$device = $em->getRepository(Device::class)->findOneBy(['uniqueId' => $uniqueId]);
		
		if (!isset($device)) // Device not found - creating new
		{
			if (!isset($name, $os) || ctype_space($uniqueId) || empty($uniqueId))
				return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);

			$device = new Device($uniqueId, $name, $os);
			$device->setLastActivity(new \DateTime('now'));
			if (isset($simCard))
				$device->setSimCard(filter_var($simCard, FILTER_VALIDATE_BOOLEAN));
			
			$em->persist($device);
			$em->flush();

			$json = $this->serializer->serialize($device, 'json', ['groups' => 'group-all']);
			return new Response($json, Response::HTTP_OK, $this->headers);
		}
		
		if (isset($name)) 	 	$device->setName($name);
		if (isset($simCard))	$device->setSimCard(filter_var($simCard, FILTER_VALIDATE_BOOLEAN));
		if (isset($os))			$device->setOs($os);
		//if (isset($enabled))
		//	$device->setEnabled(filter_var($enabled, FILTER_VALIDATE_BOOLEAN)); // filter_var = bool parse from string
		
		$em->flush();
		
		// NOT WORKING: lastActivity is missing in result json. There's some difficulties serializing DateTime type.
		$json = $this->serializer->serialize($device, 'json', ['groups' => 'group-all']);		
		return new Response($json, Response::HTTP_OK, $this->headers);
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
		
		if (!isset($uniqueId, $name, $os) || ctype_space($uniqueId) || empty($uniqueId))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		$device = new Device($uniqueId, $name, $os);
		$device->setLastActivity(new \DateTime('now'));
		if (isset($simCard))
			$device->setSimCard(filter_var($simCard, FILTER_VALIDATE_BOOLEAN));
		
		// BAD_REQUEST if there already is a device with same uniqueId
		$oldDevice = $this->getDoctrine()->getRepository(Device::class)->findOneBy(['uniqueId' => $uniqueId]);
		if (isset($oldDevice))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($device);
		$em->flush();
		
		$json = $this->serializer->serialize($device, 'json', ['groups' => 'group-all']);
		return new Response($json, Response::HTTP_OK, $this->headers);
	}
	
	/**
	 * @Route("/device/delete", name="device_delete")
	 */
	public function deleteDevice(Request $request)
	{
		// Check if argument is set
		$uniqueId = $request->request->get('uniqueId');
		if (!isset($uniqueId))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		// Get device
		$device = $this->getDoctrine()->getRepository(Device::class)->findOneBy(['uniqueId' => $uniqueId]);
		if (!isset($device))
			return new Response(null, Response::HTTP_NOT_FOUND, $this->headers);
		
		// Get all usage log entries to remove them as well
		$deviceLog = $this->getDoctrine()->getRepository(UsageEntry::class)->findBy(['device' => $device]);
		
		$em = $this->getDoctrine()->getManager();
		foreach ($deviceLog as $logEntry)
			$em->remove($logEntry);
		$em->remove($device);
		$em->flush();
		
		return new Response(null, Response::HTTP_OK, $this->headers);		
	}

	/**
	 * @Route("/device/register", name="device_register")
	 */
	public function registerDevice(Request $request)
	{
		// Check if both arguments set
		$uniqueId = $request->request->get('uniqueId');
		$userCode = $request->request->get('userCode');
		if (!isset($uniqueId, $userCode))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		// Check if both entities exist
		$device = $this->getDoctrine()->getRepository(Device::class)->findOneBy(['uniqueId' => $uniqueId]);
		$user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['qrCode' => $userCode]);
		if (!isset($device, $user))
			return new Response(null, Response::HTTP_NOT_FOUND, $this->headers);
		
		// Process device registering
		$timestamp = new \DateTime('now');
		$device->setLastUser($user);
		$device->setLastActivity($timestamp);
		$usageEntry = new UsageEntry($user, $device, $timestamp);
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($usageEntry);
		$em->flush();
		
		$json = $this->serializer->serialize(
			$usageEntry, 
			'json', ['groups' => 'group-all']
		);
		
		return new Response(
			$json,
			Response::HTTP_OK,
			$this->headers
		);
	}
	
	/**
	 * Must be at the bottom of the controller as otherwise it also catches all other routes.
	 *
	 * @Route("device/{deviceId}", name="device_single")
	 */
	public function getDevice(string $deviceId)
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
			$this->headers
		);
	}
}