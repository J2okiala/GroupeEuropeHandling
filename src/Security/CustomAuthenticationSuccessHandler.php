<?php 
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class CustomAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Récupérer les rôles de l'utilisateur connecté
        $roles = $token->getRoleNames();

        // Déterminer la redirection en fonction du rôle
        if (in_array('ROLE_EMPLOYEUR', $roles, true)) {
            $redirectUrl = $this->router->generate('profilEmployeur');
        } elseif (in_array('ROLE_CANDIDAT', $roles, true)) {
            $redirectUrl = $this->router->generate('profilCandidat');
        } else {
            $redirectUrl = $this->router->generate('home'); // Page par défaut
        }

        return new RedirectResponse($redirectUrl);
    }
}
