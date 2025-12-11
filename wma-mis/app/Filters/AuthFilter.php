<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {

        // if (!session()->has('loggedUser')) {
        //     return redirect()->to('/login');
        // }

        if (!auth()->loggedIn()) {
            return redirect()->to('/');
        } 
        else if (empty(auth()->user()->phone_number)) {
            return redirect()->to('updateMobile/' . auth()->user()->unique_id);
            
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' https://cdn.datatables.net https://cdnjs.cloudflare.com https://unpkg.com https://cdn.jsdelivr.net 'unsafe-inline'; ";
        $csp .= "style-src 'self' https://cdn.datatables.net https://cdn.jsdelivr.net https://fonts.googleapis.com 'unsafe-inline'; ";
        $csp .= "img-src 'self' data:; ";  // if you allow data URLs for images
        $csp .= "font-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com;";
        $csp .= "frame-ancestors 'self';";
        
        $response->setHeader('Content-Security-Policy', $csp);
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-Frame-Options', 'DENY');
        // Add the Strict-Transport-Security header
        $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // In a filter or middleware

    }
}
