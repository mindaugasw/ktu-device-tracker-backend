<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\UsageEntry;
use App\Entity\User;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Swagger\Annotations AS SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

class DeviceController extends AbstractController
{
	private $serializer;
	
	private $headers = ['content-type' => 'application/json', 'Access-Control-Allow-Origin' => '*'];
	
	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}
	
	/**
	 * @Route("/device/all", name="device_list", methods={"GET"})
	 * 
	 * @SWG\Get(
	 *     summary="Get devices list",
	 *     description="Gets a list of all devices on the system and their properties.",
	 * 	   produces={"application/json"},
	 *     tags={"Devices"},
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success",
	 *         @SWG\Schema(
	 *     			type="array",
	 *     			@SWG\Items(ref=@Model(type=Device::class))
	 * 		   )
	 *     )
	 * )
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
	 * @Route("/device/all/history", name="device_all_history", methods={"GET"})
	 * 
	 * @SWG\Get(
	 *     summary="Get all usage history",
	 *     description="Gets combined usage history for all devices in the system.",
	 * 	   produces={"application/json"},
	 *     tags={"Devices"},
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success",
	 *         @SWG\Schema(
	 *     			type="array",
	 *     			@SWG\Items(ref=@Model(type=UsageEntry::class))
	 * 		   )
	 *     )
	 * )
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
	 * @Route("/device/{deviceId}/history", name="device_single_history", methods={"GET"})
	 *
	 * @SWG\Get(
	 *     summary="Get device usage history",
	 *     description="Gets usage history for a single device.",
	 * 	   produces={"application/json"},
	 *     tags={"Devices"},
	 *     @SWG\Parameter(
	 *         name="deviceId",
	 *         in="path",
	 *         description="Device unique identifier (uniqueId).",
	 *         type="string",
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success",
	 *         @SWG\Schema(
	 *     			type="array",
	 *     			@SWG\Items(ref=@Model(type=UsageEntry::class))
	 * 		   )
	 *     ),
	 *     @SWG\Response(
	 *         response=404,
	 *         description="Device not found"
	 *     )
	 * )	 
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
	 * @Route("/device/update", name="device_update", methods={"POST"})
	 * 
	 * @SWG\Post(
	 *     summary="Update device info",
	 *     description="Update information about specific device: it's name, SIM card status, or OS.
				If there is no device with given uniqueId, a new device is created. In that case device name, SIM card status, and OS must all be provided.",
	 * 	   produces={"application/json"},
	 *     tags={"Devices"},
	 *     @SWG\Parameter(
	 *         name="uniqueId",
	 *         in="formData",
	 *         description="Device which to update.",
	 *         type="string",
	 *     	   required=true
	 *     ),
	 *     @SWG\Parameter(
	 *         name="name",
	 *         in="formData",
	 *         description="Updates device name. If creating new device, uniqueId must be not empty and not whitespace-only.",
	 *         type="string",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="simCard",
	 *         in="formData",
	 *         description="Updates SIM card status.",
	 *         type="boolean",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="os",
	 *         in="formData",
	 *         description="Updates device OS.",
	 *         type="string",
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success. Returns updated or newly created device.",
	 *         @SWG\Schema(ref=@Model(type=Device::class)
	 * 		   )
	 *     ),
	 *     @SWG\Response(
	 *         response=400,
	 *         description="Bad request. Returns if:
	               * there's no uniqueId provided
	               * creating new device and name or OS are not provided"
	 *     ),
	 * )
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
	 * @Route("/device/new", name="device_new", methods={"POST"})
	 *
	 * @SWG\Post(
	 *     summary="Create new device",
	 *     description="Creates new device. It's also possible to create new device by sending new uniqueId to /device/new .",
	 * 	   produces={"application/json"},
	 *     deprecated=true,
	 *     tags={"Devices"},
	 *     @SWG\Parameter(
	 *         name="deviceId",
	 *         in="formData",
	 *         description="Device unique identifier (uniqueId).",
	 *         type="string",
	 *     	   required=true
	 *     ),
	 *     @SWG\Parameter(
	 *         name="name",
	 *         in="formData",
	 *         description="Device name. Must be not empty and not whitespace-only.",
	 *         type="string",
	 *     	   required=true,
	 *     ),
	 *     @SWG\Parameter(
	 *         name="simCard",
	 *         in="formData",
	 *         description="SIM card status.",
	 *         type="boolean",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="os",
	 *         in="formData",
	 *         description="Device OS.",
	 *         type="string",
	 *         required=true,
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success. Returns newly created device.",
	 *         @SWG\Schema(ref=@Model(type=Device::class)
	 * 		   )
	 *     ),
	 *     @SWG\Response(
	 *         response=400,
	 *         description="Bad request. Returns if:
	 * uniqueId, name, or OS are not provided
	 * uniqueId is empty or whitespace-only
	 * there already exists device with given uniqueId"
	 *     ),
	 * )
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
	 * @Route("/device/delete", name="device_delete", methods={"POST"})
	 *
	 * @SWG\Post(
	 *     summary="Delete device",
	 *     description="Deletes device.",
	 * 	   produces={"application/json"},
	 *     tags={"Devices"},
	 *     @SWG\Parameter(
	 *         name="uniqueId",
	 *         in="formData",
	 *         description="Device unique identifier (uniqueId).",
	 *         type="string",
	 *     	   required=true
	 *     ),
	 *     @SWG\Parameter(
	 *         name="token",
	 *         in="formData",
	 *         description="JWT authorization token.",
	 *         type="string",
	 *     	   required=true
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success.",
	 *     ),
	 *     @SWG\Response(
	 *         response=400,
	 *         description="Bad request. Returns if uniqueId or JWT not provided."
	 *     ),
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Could not authorize (invalid/expired token)."
	 *     ),
	 *     @SWG\Response(
	 *         response=404,
	 *         description="Device with given uniqueId not found."
	 *     ),
	 * )
	 */
	public function deleteDevice(Request $request, AuthService $authService)
	{
		if (!$authService->verify($request))
			return new Response(null, Response::HTTP_UNAUTHORIZED, $this->headers);
		
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
	 * @Route("/device/register", name="device_register", methods={"POST"})
	 *
	 * @SWG\Post(
	 *     summary="Register device",
	 *     description="Register device to user (to be used when person takes device and scans his QR code with it).",
	 * 	   produces={"application/json"},
	 *     tags={"Devices"},
	 *     @SWG\Parameter(
	 *         name="uniqueId",
	 *         in="formData",
	 *         description="Device unique identifier (uniqueId).",
	 *         type="string",
	 *     	   required=true
	 *     ),
	 *     @SWG\Parameter(
	 *         name="userCode",
	 *         in="formData",
	 *         description="User's QR code.",
	 *         type="string",
	 *     	   required=true
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success.",
	 *         @SWG\Schema(ref=@Model(type=UsageEntry::class))
	 *     ),
	 *     @SWG\Response(
	 *         response=400,
	 *         description="Bad request. Returns if uniqueId or userCode are not provided."
	 *     ),
	 *     @SWG\Response(
	 *         response=404,
	 *         description="Device or user not found."
	 *     ),
	 * )
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
	 * @Route("/device/{deviceId}", name="device_single", methods={"GET"})
	 * 
	 * @SWG\Get(
	 *     summary="Get single device",
	 *     description="Gets info about a single device.",
	 * 	   produces={"application/json"},
	 *     tags={"Devices"},
	 *     @SWG\Parameter(
	 *         name="deviceId",
	 *         in="path",
	 *         description="Device unique identifier (uniqueId).",
	 *         type="string",
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success",
	 *         @SWG\Schema(ref=@Model(type=Device::class))
	 *     ),
	 * 	   @SWG\Response(
	 *         response=404,
	 *         description="Device not found",
	 *     )
	 * )
	 * 
	 * Method nust be at the bottom of the controller as otherwise it also catches all other routes.
	 */
	public function getDevice(string $deviceId)
	{
		$device = $this->getDoctrine()
			->getRepository(Device::class)
			->findOneBy(['uniqueId' => $deviceId]);
		
		if (!isset($device))
			return new Response(null, Response::HTTP_NOT_FOUND, $this->headers);
		
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