<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoderSubscriber implements EventSubscriberInterface
{
   /**
    * @var UserPasswordHasherInterface
    */
   private $encoder;

   public function __construct(UserPasswordHasherInterface $encoder)
   {
      $this->encoder = $encoder;
   }

   public static function getSubscribedEvents()
   {
      return [
         KernelEvents::VIEW => ['encodePassword', EventPriorities::PRE_WRITE]
      ];
   }

   public function encodePassword(ViewEvent $event)
   {
      $result = $event->getControllerResult();
      $method = $event->getRequest()->getMethod();

      if ($result instanceof User && $method === "POST") {
         $hash = $this->encoder->hashPassword($result, $result->getPassword());
         $result->setPassword($hash);
      }
   }
}
