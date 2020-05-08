<?php

namespace PlayOrPay\Tests\Unit\Application\User;

use League\Tactician\CommandBus;
use PlayOrPay\Application\Command\User\User\Activate\ActivateUserCommand;
use PlayOrPay\Application\Command\User\User\Deactivate\DeactivateUserCommand;
use PlayOrPay\Domain\User\User;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChangeActivityTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel();
    }

    /** @test */
    public function should_activate_properly(): void
    {
        $this->should_has_specific_active_state_after_command(true, ActivateUserCommand::class);
    }

    /** @test */
    public function should_deactivate_properly(): void
    {
        $this->should_has_specific_active_state_after_command(false, DeactivateUserCommand::class);
    }

    private function should_has_specific_active_state_after_command(bool $newExpectedState, string $commandClass): void
    {
        /** @var UserRepository $userRepo */
        $userRepo = self::$container->get(UserRepository::class);

        $user = $this->makeUser(!$newExpectedState);

        /* @noinspection PhpUnhandledExceptionInspection */
        $userRepo->save($user);

        $user = $userRepo->find($user->getSteamId());

        $this->assertTrue($user->isActive() !== $newExpectedState, 'User must be in another state at the start');

        /** @var CommandBus $bus */
        $bus = self::$container->get(CommandBus::class);

        $bus->handle(new $commandClass($user->getSteamId()));

        $this->assertTrue($user->isActive() === $newExpectedState, 'Unexpected state at the end');
    }

    private function makeUser(bool $active): User
    {
        $user = (new User())
            ->setSteamId(mt_rand(1, 9001))
            ->setProfileName('abc')
            ->setProfileUrl('https://steam/ab/c/')
            ->setAvatar('https://steam/ab/c.jpg');

        $active ? $user->activate() : $user->deactivate();

        return $user;
    }
}
