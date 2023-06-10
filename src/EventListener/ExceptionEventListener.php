<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener(event: 'kernel.exception')]
class ExceptionEventListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $error = $event->getThrowable();
        if(!$error instanceof NotFoundHttpException){
            return;
        }
        $request = $event->getRequest();
        $language = $request->getPreferredLanguage();
        
        $request = $event->getRequest();
        if(!$this->startsWithValidLanguage($request)){
            $response = new Response(status: 302);
            $response->headers->add(['Location' => "$language".$request]);
        }
        $acceptLanguageHeaders = $request
            ->headers
            ->get('Accept-Language'
            );
        $languages = explode(',', $acceptLanguageHeaders);
        $language = str_replace('-','_',explode(';',$languages[0])[0]);
        
        if(!str_starts_with($request->getPathInfo(), "/$language")){
            $response = new Response(status: 302);
            $response->headers->add(['Location' => "/$language".$request->getPathinfo()]);
            $event->setResponse($response);
        }
    }
    public function startsWithValidLanguage(Request $request): bool
    {
        $validLanguages = ['en', 'pt_BR'];
        foreach ($validLanguages as $language){
            if(str_starts_with($request->getPathInfo(), "/$language")){
                return true;
            }
        }
        return false;
    }
}