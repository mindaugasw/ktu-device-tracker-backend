<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\UsageEntry;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
	
	private $serializer;
	
	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}
	
	/**
	 * @Route("/user/all", name="user_list")
	 */
	public function getUsersList()
	{
		$users = $this->getDoctrine()
			->getRepository(User::class)
			->findAll();
		
		$json = $this->serializer->serialize(
			$users,
			'json', ['groups' => 'group-all']
		);
		
		return new Response(
			$json,
			Response::HTTP_OK,
			['content-type' => 'application/json']
		);
	}
	
	/**
	 * @Route("user/update", name="user_update")
	 */
	public function updateUser(Request $request)
	{
		$id = $request->request->get('id');
		if (!isset($id))
			return new Response(null, Response::HTTP_BAD_REQUEST);
		
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository(user::class)->find($id);
		
		if (!isset($user))
			return new Response(null, Response::HTTP_NOT_FOUND);
		
		$name = $request->request->get('name');
		$surname = $request->request->get('surname');
		$office = $request->request->get('office');
		$floor = $request->request->get('floor');
		
		if (isset($name)) 	 	$user->setName($name);
		if (isset($surname))	$user->setSurname($surname);
		if (isset($office))		$user->setOffice($office);
		if (isset($floor))		$user->setFloor($floor);
		
		$em->flush();
		
		$json = $this->serializer->serialize($user, 'json', ['groups' => 'group-all']);
		return new Response($json, Response::HTTP_OK, ['content-type' => 'application/json']);
	}
	
	/**
	 * @Route("/user/new", name="user_new")
	 */
	public function createNewUser(Request $request)
	{
		$name = $request->request->get('name');
		$surname = $request->request->get('surname');
		$office = $request->request->get('office');
		$floor = $request->request->get('floor');
		
		if (!isset($name, $surname, $office, $floor))
			return new Response(null, Response::HTTP_BAD_REQUEST);
		
		// In case there already is a user with the same QR code, regenerate it
		do {
			$newUser = new user($name, $surname, $office, $floor);
			$olduser = $this->getDoctrine()->getRepository(user::class)->findOneBy(['qrCode' => $newUser->getQrCode()]);			
		} while (isset($olduser));
				
		$em = $this->getDoctrine()->getManager();
		$em->persist($newUser);
		$em->flush();
		
		$json = $this->serializer->serialize($newUser, 'json', ['groups' => 'group-all']);
		return new Response($json, Response::HTTP_OK, ['content-type' => 'application/json']);
	}
	
	/**
	 * @Route("/user/delete", name="user_delete")
	 */
	public function deleteUser(Request $request)
	{
		// Check if parameter is set
		$id = $request->request->get('id');
		if (!isset($id))
			return new Response(null, Response::HTTP_BAD_REQUEST);
		
		// Check if user exists
		$user = $this->getDoctrine()->getRepository(User::class)->find($id);
		if (!isset($user))
			return new Response(null, Response::HTTP_NOT_FOUND);
		
		// Remove user from all devices
		$userDevices = $this->getDoctrine()->getRepository(Device::class)->findBy(['lastUser' => $user]);
		foreach ($userDevices as $device)
			$device->setLastUser(null);

		$em = $this->getDoctrine()->getManager();
		
		// Remove all user usage history/log
		$userLog = $this->getDoctrine()->getRepository(UsageEntry::class)->findBy(['user' => $user]);
		foreach ($userLog as $logEntry)
			$em->remove($logEntry);
		
		$em->remove($user);
		$em->flush();
		
		return new Response(null, Response::HTTP_OK);
	}
	
	/**
	 * Must be at the bottom of the controller as otherwise it also catches all other routes.
	 *
	 * @Route("user/{id}", name="user_single")
	 */
	public function getUserSingle(int $id)
	{
		$user = $this->getDoctrine()
			->getRepository(user::class)
			->find($id);
		
		if (!isset($user))
			return new Response(null, Response::HTTP_NOT_FOUND);
		
		$json = $this->serializer->serialize(
			$user,
			'json', ['groups' => 'group-all']
		);
		
		return new Response(
			$json,
			Response::HTTP_OK,
			['content-type' => 'application/json']
		);
	}
	
	
}
