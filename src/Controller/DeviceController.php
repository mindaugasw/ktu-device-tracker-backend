<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeviceController extends AbstractController
{
	/**
	 * @Route("/testX", name="test_method")
	 */
	public function testMethod()
	{
		return new Response("xD");
	}
	
	/**
	 * @Route("/devices", name="device_list")
	 */
	public function getDeviceList()
	{
		
	}
}