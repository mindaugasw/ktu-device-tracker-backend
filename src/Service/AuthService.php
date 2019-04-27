<?php


namespace App\Service;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service for authorization handling
 */
class AuthService
{
	private const JWT_KEY = "vlEIkJeG3soQ4Ft24ocJ58ZUXgjsssIx";
	
	/**
	 * Verify that this request is from authorized user.
	 * 
	 * @param Request $request
	 * @return boolean Returns true if user is athorized. False otherwise.
	 */
	public function verify(Request $request)
	{
		if ($request->isMethod('GET'))
			$token = $request->query->get('token');
		else if ($request->isMethod('POST'))
			$token = $request->request->get('token');
		
		if (!isset($token))
		{
			//$data = '400 Bad request';
			return false;
		}
		
		try
		{
			$decoded = JWT::decode($token, self::JWT_KEY, ['HS256']);
			//$data = (array) $decoded;
			return true;
		} catch (\Exception $ex)
		{
			//$data = $ex->getMessage();
			return false;
		}	
	}
	
	/**
	 * If login is successful, API should return Json Web Token (JWT) to later be used
	 * for endpoints that require authorization.
	 * 
	 * @param int $id User's ID in the database
	 * @param string $username Username
	 * @return string Json Web Token (JWT)
	 */
	public function getJWT(int $id, string $username)
	{
		$token = [
			'iat' => time(),
			'exp' => time() + 3600 * 24 * 7, // expires in a week
			'data' => [
				'id' => $id,
				'username' => $username
			]
		];
		
		return JWT::encode($token, self::JWT_KEY);
	}
}