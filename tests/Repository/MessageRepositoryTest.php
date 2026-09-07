<?php

namespace App\Tests\Repository;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private MessageRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->em->getRepository(Message::class);
    }

    public function testCountUnread(): void
    {
        $count = $this->repository->countUnread();
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountAll(): void
    {
        $count = $this->repository->countAll();
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
