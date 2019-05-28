<?php

namespace App\Command;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateAccountCommand extends Command
{
    protected static $defaultName = 'app:create-account';	
	private $em;
	
	public function __construct(EntityManagerInterface $em)
	{
		parent::__construct();
		$this->em = $em;
	}
    
    protected function configure()
    {
        $this
            ->setDescription('Creates a new admin account with provided username and password (since there is no registration or account management in the app).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);        
        
        $helper = $this->getHelper('question');
		$question1 = new Question('Enter username: ');
		$question2 = new Question('Enter password: ');
		$question2->setHidden(true);
		$question2->setHiddenFallback(false);
        
		$username = $helper->ask($input, $output, $question1);
		if (!isset($username) || ctype_space($username) || empty($username))
		{
			$io->error('Username cannot be empty or whitespace-only!');
			return;
		}
	
		$io->warning('Passwords are saved in plain text! Don\'t reuse password from other services!');
	
		$password = $helper->ask($input, $output, $question2);
		if (!isset($password) || ctype_space($password) || empty($password))
		{
			$io->error('Password cannot be empty or whitespace-only!');
			return;
		}
		
		$io->text('');	
		try
		{
			$account = new Account($username, $password);
			$this->em->persist($account);
			$this->em->flush();
			$io->success("New account {$username} successfully created!");
		}
		catch (\Exception $ex)
		{
			$io->error('Could not insert account into database!');
		}
    }
}
