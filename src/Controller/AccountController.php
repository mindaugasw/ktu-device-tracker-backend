<?php

namespace App\Controller;

use App\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Firebase\JWT\JWT;
use Swagger\Annotations AS SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api")
 */
class AccountController extends AbstractController
{
	private const JWT_KEY = "vlEIkJeG3soQ4Ft24ocJ58ZUXgjsssIx";
	
	private $serializer;
	
	private $headers = ['content-type' => 'application/json', 'Access-Control-Allow-Origin' => '*'];
	
	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}
	
    /**
     * @Route("/account/all", name="account_list", methods={"GET"})
	 */
    public function getAccountsList(Request $request)
	{	
		//$authHeader = $request->headers->get('Authorization');
		
		$token = $request->query->get('token');
		
		//if (!isset($authHeader))
		if (!isset($token))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
	
		//$token = explode(' ', $authHeader)[1];
		try
		{
			$decoded = JWT::decode($token, self::JWT_KEY, ['HS256']);
		} catch (\Exception $ex) {
			return new Response($ex->getMessage(), Response::HTTP_UNAUTHORIZED, $this->headers);
		}
		
		$accounts = $this->getDoctrine()->getRepository(Account::class)->findAll();
		$json = $this->serializer->serialize($accounts, 'json', ['groups' => 'group-all']);	
		return new Response(
			$json,
			Response::HTTP_OK,
			$this->headers
		);
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
    public function logIn(Request $request)
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
		
		$token = [
			'iat' => time(),
			'exp' => time() + 3600,
			'data' => [
				'id' => $account->getId(),
				'username' => $account->getUsername()
			]
		];
		
		$jwt = JWT::encode($token, self::JWT_KEY);
		
		return new Response(
			$jwt,
			Response::HTTP_OK,
			$this->headers
		);
	}
}
