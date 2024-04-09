<?php
// src/Controller/AdminController.php
namespace App\Controller;
// Allow to send a response
use Symfony\Component\HttpFoundation\Response;
// Allow to link to a route
use Symfony\Component\Routing\Attribute\Route;
// Allow additionnal methods like rendering template, redirect, generate url...
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// Add prefix to all routes inside this controller
#[Route(path: '/admin')]
class AdminController extends AbstractController
{
    // Redirect to login or dashboard
    #[Route(path: '/', name: 'admin_redirect')]
    public function admin_redirect(): Response
    {
        // Check if user is logged in and role_admin
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->redirectToRoute('app_login');
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Case already logged in, redirect to dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }
        // Else show login form
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    // Route to link with and name to identify it
    #[Route('/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    // Get parameter from request
    public function admin_dashboard(): Response
    {
    // Return a response
        return $this->render('admin/dashboard.html.twig');
    }

}