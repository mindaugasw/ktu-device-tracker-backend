<?php

namespace App\Controller;

use App\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Firebase\JWT\JWT;

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
     * @Route("/account/all", name="account_list")
     */
    public function getAccountsList(Request $request)
	{	
		$authHeader = $request->headers->get('Authorization');
		
		if (!isset($authHeader))
			return new Response(null, Response::HTTP_BAD_REQUEST, $this->headers);
	
		$token = explode(' ', $authHeader)[1];
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
	 * @Route("/login", name="login")
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
			'exp' => time() + 600,
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
