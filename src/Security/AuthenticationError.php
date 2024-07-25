<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthenticationError implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
       
        if ($exception->getMessageKey() === 'Bad credentials.' || $exception->getMessageKey() === 'Invalid credentials.') {

            $message = "Identifiants incorrects.";
        } 

        $data = [
            'error' => $exception->getMessageKey(),
            'message' => $message,
        ];

        return new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED);
    }
}
