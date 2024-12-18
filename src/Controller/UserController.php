<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserPasswordType;
use App\Form\User\UserRoleType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    )
    {
    }
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route("/users", name: "user_list", methods: ["GET"])]
    public function listAction(): Response
    {
        return $this->render('user/list.html.twig', ['users' => $this->userService->findAll()]);
    }
    
    /**
     * @throws TransportExceptionInterface
     */
    #[Route("/users/create", name: "user_create", methods: ["GET", "POST"])]
    public function createAction(Request $request): Response
    {
        $user = new User();
        $formType = $this->isGranted('ROLE_ADMIN') ? UserRoleType::class : UserPasswordType::class;
        $form = $this->createForm($formType, $user);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($this->isGranted('ROLE_ADMIN')) {
                $this->userService->sendPasswordMail($user);
            }
            
            $this->userService->encoderPassword($user);
            $this->userService->save($user);
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            
            return $this->redirectToRoute('user_list');
        }
        
        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }
    
    #[Route("/users/{id}/edit", name: "user_edit", methods: ["GET", "POST"])]
    public function editAction(User $user, Request $request): Response
    {
        
        $formType = $this->isGranted('ROLE_ADMIN') ? UserRoleType::class : UserPasswordType::class;
        $form = $this->createForm($formType, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->encoderPassword($user);
            $this->userService->save($user);
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            
            return $this->redirectToRoute('user_list');
        }
        
        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
