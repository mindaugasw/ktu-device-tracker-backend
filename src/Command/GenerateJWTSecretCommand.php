<?php

namespace App\Command;

use App\Utils\RandomString;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateJWTSecretCommand extends Command
{
    protected static $defaultName = 'app:generate-JWT-secret';

    protected function configure()
    {
        $this
            ->setDescription('Generates a JWT secret key and puts it into /src/Utils/JWTsecret.php . Can also be used to regenerate a key.');
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $file = './src/Utils/JWTsecret.php';
		$handle = fopen($file, 'w') or die('ERROR: cannot open file:  '.$file); //open file for writing ('w','r','a')...
        $secretKey = RandomString::generateRandomString(32);
        $fileText =
			"<?php\n\nnamespace App\Utils;\n\nclass JWTsecret\n{\n\tpublic static function getJWTSecret()\n\t{\n\t\treturn '".
			$secretKey.
			"';\n\t}\n}";
        fwrite($handle, $fileText);
        $io->text("Successfully generated new JWT secret key.\n");
        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
