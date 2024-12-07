<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserPasswordType;
use App\Form\User\UserRoleType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    #[Route("/admin/users", name: "user_list", methods: ["GET"])]
    public function listAction(): Response
    {
        return $this->render('user/list.html.twig', ['users' => $this->userService->findAll()]);
    }
    
    /**
     * @throws TransportExceptionInterface
     */
    #[Route("/users/create", name: "user_create", methods: ["GET", "POST"])]
    public function createAction(Request $request): Response|RedirectResponse
    {
        $user = new User();
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        
        if ($isAdmin) {
            $this->userService->generatePassword($user);
        }
        
        $formType = $this->determineFormType();
        $form = $this->createForm($formType, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->processUserCreation($user, $isAdmin);
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            
            return $this->redirectToAppropriateRoute($isAdmin, 'app_login');
        }
        
        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }
    
    private function determineFormType(): string
    {
        return $this->isGranted('ROLE_ADMIN') ? UserRoleType::class : UserPasswordType::class;
    }
    
    /**
     * @throws TransportExceptionInterface
     */
    private function processUserCreation(User $user, bool $isAdmin): void
    {
        if ($isAdmin) {
            $this->userService->sendPasswordMail($user);
        }
        
        $this->userService->encoderPassword($user);
        $this->userService->save($user);
    }
    
    private function redirectToAppropriateRoute(bool $isAdmin, string $nonAdminRoute): RedirectResponse
    {
        $routeName = $isAdmin ? 'user_list' : $nonAdminRoute;
        return $this->redirectToRoute($routeName);
    }
    
    #[Route("/users/{id}/edit", name: "user_edit", methods: ["GET", "POST"])]
    public function editAction(User $user, Request $request): Response
    {
        if (!$this->isUserAllowedToEdit($user)) {
            $this->addFlash('error', "Vous n'avez pas les droits pour modifier cet utilisateur.");
            return $this->redirectToRoute('homepage');
        }
        
        $formType = $this->determineFormType();
        $form = $this->createForm($formType, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->processUserUpdate($user);
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            
            return $this->redirectToAppropriateRoute($this->isGranted('ROLE_ADMIN'), 'homepage');
        }
        
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
    
    private function isUserAllowedToEdit(User $user): bool
    {
        return $this->getUser() === $user || $this->isGranted('ROLE_ADMIN');
    }
    
    private function processUserUpdate(User $user): void
    {
        $this->userService->encoderPassword($user);
        $this->userService->save($user);
    }
    
}
