<?php

namespace App\Controller;

use App\Entity\Account;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Swagger\Annotations AS SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

class AccountController extends AbstractController
{	
	private $serializer;
	
	private $headers = ['content-type' => 'application/json', 'Access-Control-Allow-Origin' => '*'];
	
	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}
	
	/**
	 * @Route("/login", name="login", methods={"POST"})
	 * 
	 * @SWG\Post(
	 *     summary="Log in",
	 *     description="Log in to the system and get Json Web Token (JWT) that is used for authorization on admin-only endpoints.",
	 * 	   produces={"application/json"},
	 *     tags={"Other"},
	 *     @SWG\Parameter(
	 *         name="username",
	 *         in="formData",
	 *         description="Username.",
	 *         type="string",
	 *     	   required=true,
	 *     ),
	 *     @SWG\Parameter(
	 *         name="password",
	 *         in="formData",
	 *         description="Password.",
	 *         type="string",
	 *     	   required=true,
	 *     ),
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Success. Returns Json Web Token (JWT). Info in the payload: token issue timestamp, expiration timestamp, user's ID and username.",
	 *     ),
	 * 	   @SWG\Response(
	 *         response=400,
	 *         description="Username or password not provided",
	 *     ),
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Wrong username and/or password.",
	 *     )
	 * )
	 */
    public function logIn(Request $request, AuthService $authService)
	{
		$username = $request->request->get('username');
		$password = $request->request->get('password');
		
		if (!isset($username, $password))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
		
		$account = $this->getDoctrine()->getRepository(Account::class)
			->findOneBy(['username' => $username, 'password' => $password]);
		
		if (!isset($account))
			return new Response(null, Response::HTTP_UNAUTHORIZED, $this->headers);
		
		// Credentials are valid - generate JWT
		$jwt = $authService->getJWT($account->getId(), $account->getUsername());
		
		return new Response(
			$jwt,
			Response::HTTP_OK,
			$this->headers
		);
	}
}
