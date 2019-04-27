<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\UsageEntry;
use App\Entity\User;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Swagger\Annotations AS SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
	private $serializer;

	private $headers = ['content-type' => 'application/json', 'Access-Control-Allow-Origin' => '*'];
	
	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}
	
	/**
	 * @Route("/user/all", name="user_list", methods={"GET"})
	 * 
	 * @SWG\Get(
	 *     summary="Get users list",
	 *     description="Gets a list of all users on the system and their info.",
	 * 	   produces={"application/json"},
	 *     tags={"Users"},
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success",
	 *         @SWG\Schema(
	 *              type="array",
	 *              @SWG\Items(ref=@Model(type=User::class))
	 *         )
	 *     )
	 * )
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
			$this->headers
		);
	}
	
	/**
	 * @Route("/user/update", name="user_update", methods={"POST"})
	 *
	 * @SWG\Post(
	 *     summary="Update user info",
	 *     description="Update information about specific user: it's name, surname, office, floor, qrCode.",
	 * 	   produces={"application/json"},
	 *     tags={"Users"},
	 *     @SWG\Parameter(
	 *         name="id",
	 *         in="formData",
	 *         description="User ID in the database.",
	 *         type="integer",
	 *     	   required=true
	 *     ),
	 *     @SWG\Parameter(
	 *         name="token",
	 *         in="formData",
	 *         description="JWT authorization token.",
	 *         type="string",
	 *     	   required=true
	 *     ),
	 *     @SWG\Parameter(
	 *         name="name",
	 *         in="formData",
	 *         description="Update user's name.",
	 *         type="string",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="surname",
	 *         in="formData",
	 *         description="Update user's surname.",
	 *         type="string",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="office",
	 *         in="formData",
	 *         description="Update user's office.",
	 *         type="string",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="floor",
	 *         in="formData",
	 *         description="Update user's floor.",
	 *         type="integer",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="qrCode",
	 *         in="formData",
	 *         description="Update user's qrCode.",
	 *         type="string",
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success. Returns updated user.",
	 *         @SWG\Schema(ref=@Model(type=User::class))
	 *     ),
	 *     @SWG\Response(
	 *         response=400,
	 *         description="Bad request. Returns if:
	 * id or JWT not provided
	 * there already is another user with given qrCode"
	 *     ),
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Could not authorize (invalid/expired token)."
	 *     ),
	 *     @SWG\Response(
	 *         response=404,
	 *         description="User not found"
	 *     ),
	 * )
	 */
	public function updateUser(Request $request, AuthService $authService)
	{
		if (!$authService->verify($request))
			return new Response(null, Response::HTTP_UNAUTHORIZED, $this->headers);
		
		$id = $request->request->get('id');
		if (!isset($id))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository(user::class)->find($id);
		
		if (!isset($user))
			return new Response(null, Response::HTTP_NOT_FOUND, $this->headers);
		
		$name = $request->request->get('name');
		$surname = $request->request->get('surname');
		$office = $request->request->get('office');
		$floor = $request->request->get('floor');
		$qrCode = $request->request->get('qrCode');
		
		if (isset($name)) 	 	$user->setName($name);
		if (isset($surname))	$user->setSurname($surname);
		if (isset($office))		$user->setOffice($office);
		if (isset($floor))		$user->setFloor($floor);
		if (isset($qrCode))
		{
			$olduser = $this->getDoctrine()->getRepository(user::class)->findOneBy(['qrCode' => $qrCode]);
			/* Return error if:
			 *     there already is a user with given QR code and that user isn't the currently updated one
			 *     given QR code is empty or whitespace-only string
			 */
			if ((isset($olduser) && $olduser->getId() != $id) || ctype_space($qrCode) || empty($qrCode))
				return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
			else
				$user->setQrCode($qrCode);
		}
		
		$em->flush();
		
		$json = $this->serializer->serialize($user, 'json', ['groups' => 'group-all']);
		return new Response($json, Response::HTTP_OK, $this->headers);
	}
	
	/**
	 * @Route("/user/new", name="user_new", methods={"POST"})
	 *
	 * @SWG\Post(
	 *     summary="Create new user",
	 *     description="Creates new user.",
	 * 	   produces={"application/json"},
	 *     tags={"Users"},
	 *     @SWG\Parameter(
	 *         name="token",
	 *         in="formData",
	 *         description="JWT authorization token.",
	 *         type="string",
	 *     	   required=true
	 *     ),
	 *     @SWG\Parameter(
	 *         name="name",
	 *         in="formData",
	 *         description="User's name.",
	 *         type="string",
	 *         required=true,
	 *     ),
	 *     @SWG\Parameter(
	 *         name="surname",
	 *         in="formData",
	 *         description="User's surname.",
	 *         type="string",
	 *         required=true,
	 *     ),
	 *     @SWG\Parameter(
	 *         name="office",
	 *         in="formData",
	 *         description="User's office.",
	 *         type="string",
	 *         required=true,
	 *     ),
	 *     @SWG\Parameter(
	 *         name="floor",
	 *         in="formData",
	 *         description="User's floor.",
	 *         type="integer",
	 *         required=true,
	 *     ),
	 *     @SWG\Parameter(
	 *         name="qrCode",
	 *         in="formData",
	 *         description="User's qrCode. If not provided, will be generated random string. Can be changed later.
	               If provided, must be not empty and not whitespace-only.",
	 *         type="string",
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success. Returns newly created user.",
	 *         @SWG\Schema(ref=@Model(type=User::class)
	 * 		   )
	 *     ),
	 *     @SWG\Response(
	 *         response=400,
	 *         description="Bad request. Returns if:
	 * name, surname, office, or floor are not provided
	 * give qrCode is empty or whitespace-only
	 * there already exists a user with given qrCode"
	 *     ),
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Could not authorize (invalid/expired token)."
	 *     ),
	 * )

	 */
	public function createNewUser(Request $request, AuthService $authService)
	{
		if (!$authService->verify($request))
			return new Response(null, Response::HTTP_UNAUTHORIZED, $this->headers);
		
		$name = $request->request->get('name');
		$surname = $request->request->get('surname');
		$office = $request->request->get('office');
		$floor = $request->request->get('floor');
		$qrCode = $request->request->get('qrCode');
		
		if (!isset($name, $surname, $office, $floor))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		if (!isset($qrCode)) // If QR code was not set, generate a random one.
		{
			// In case there already is a user with the same QR code, regenerate it
			do
			{
				$qrCode = User::getRandomQrCode();
				$olduser = $this->getDoctrine()->getRepository(user::class)->findOneBy(['qrCode' => $qrCode]);
			} while (isset($olduser));
		}
		else
		{
			// Check if given QR code is not already in use by other user
			$olduser = $this->getDoctrine()->getRepository(user::class)->findOneBy(['qrCode' => $qrCode]);
			if(isset($olduser) || ctype_space($qrCode) || empty($qrCode))
				return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		}

		$newUser = new user($name, $surname, $office, $floor, $qrCode);
			
		$em = $this->getDoctrine()->getManager();
		$em->persist($newUser);
		$em->flush();
		
		$json = $this->serializer->serialize($newUser, 'json', ['groups' => 'group-all']);
		return new Response($json, Response::HTTP_OK, $this->headers);
	}
	
	/**
	 * @Route("/user/delete", name="user_delete", methods={"POST"})
	 * 
	 * @SWG\Post(
	 *     summary="Delete user",
	 *     description="Deletes user.",
	 * 	   produces={"application/json"},
	 *     tags={"Users"},
	 *     @SWG\Parameter(
	 *         name="id",
	 *         in="formData",
	 *         description="User ID in the database.",
	 *         type="integer",
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
	 *         description="Bad request. Returns if id or JWT not provided."
	 *     ),
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Could not authorize (invalid/expired token)."
	 *     ),
	 *     @SWG\Response(
	 *         response=404,
	 *         description="User not found."
	 *     ),
	 * )
	 */
	public function deleteUser(Request $request, AuthService $authService)
	{
		if (!$authService->verify($request))
			return new Response(null, Response::HTTP_UNAUTHORIZED, $this->headers);
		
		$id = $request->request->get('id');
		if (!isset($id))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		// Check if user exists
		$user = $this->getDoctrine()->getRepository(User::class)->find($id);
		if (!isset($user))
			return new Response(null, Response::HTTP_NOT_FOUND, $this->headers);
		
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
		
		return new Response(null, Response::HTTP_OK, $this->headers);
	}
	
	/**
	 * @Route("/user/{id}", name="user_single", methods={"GET"})
	 * 
	 * @SWG\Get(
	 *     summary="Get single user",
	 *     description="Gets info about a single user.",
	 * 	   produces={"application/json"},
	 *     tags={"Users"},
	 *     @SWG\Parameter(
	 *         name="id",
	 *         in="path",
	 *         description="User ID in the database.",
	 *         type="integer",
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success",
	 *         @SWG\Schema(ref=@Model(type=User::class))
	 *     ),
	 * 	   @SWG\Response(
	 *         response=404,
	 *         description="User not found",
	 *     )
	 * )
	 * 
	 * Method must be at the bottom of the controller as otherwise it also catches all other routes.
	 */
	public function getUserSingle(int $id)
	{
		$user = $this->getDoctrine()
			->getRepository(user::class)
			->find($id);
		
		if (!isset($user))
			return new Response(null, Response::HTTP_NOT_FOUND, $this->headers);
		
		$json = $this->serializer->serialize(
			$user,
			'json', ['groups' => 'group-all']
		);
		
		return new Response(
			$json,
			Response::HTTP_OK,
			$this->headers
		);
	}
	
	
}
