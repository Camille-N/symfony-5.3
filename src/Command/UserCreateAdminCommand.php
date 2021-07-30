<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateAdminCommand extends Command
{
    protected static $defaultName = 'app:user:create-admin';
    protected static $defaultDescription = 'Add a short description for your command';

    private UserPasswordHasherInterface $passwordEncoder;
    private ObjectManager $manager;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, ManagerRegistry $registry)
    {
        parent::__construct(self::$defaultName);
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $registry->getManager();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('firstName', InputArgument::OPTIONAL, 'First Name')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name')
        ;
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = $this->getHelper('question');
        $email = new Question ('Your email :');
        $input->setArgument('email', $questions->ask($input, $output, $email));
        $password = new Question ('Your password :');
        $input->setArgument('password', $questions->ask($input, $output, $password));
        $firstName = new Question ('Your firstName :');
        $input->setArgument('firstName', $questions->ask($input, $output, $firstName));
        $name = new Question ('Your name :');
        $input->setArgument('name', $questions->ask($input, $output, $name));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $firstName = $input->getArgument('firstName');
        $name = $input->getArgument('name');

        // if ($email) {
        //     $io->note(sprintf('You passed an argument: %b', $email === ""));
        //     $io->note(sprintf('You passed an argument: %s', $email));
        // }
    
        $user = new User ();
        $user->setEmail($email);
        $user->addRole('ROLE_ADMIN');
        $user->setFirstName($firstName);
        $user->setName($name);

        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            $password
        ));

        $manager = $this->manager;
        $manager->persist($user);
        $manager->flush();


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

}
