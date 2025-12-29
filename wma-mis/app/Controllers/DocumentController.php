<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DocumentController extends Controller
{
    private $backendApiUrl = 'http://localhost:8080/api/document';
    private $apiKey = 'osa_approval_api_key_12345';
    
    public function view($attachmentId)
    {
        // Call backend API to get document
        $url = $this->backendApiUrl . '/view/' . $attachmentId;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $this->apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return $this->response->setStatusCode($httpCode)->setBody('Document not found or error occurred');
        }
        
        // Return the document with appropriate headers
        return $this->response
            ->setHeader('Content-Type', $contentType ?: 'application/pdf')
            ->setHeader('Content-Disposition', 'inline')
            ->setBody($response);
    }
    
    public function download($attachmentId)
    {
        // Call backend API to download document
        $url = $this->backendApiUrl . '/download/' . $attachmentId;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $this->apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return $this->response->setStatusCode($httpCode)->setBody('Document not found or error occurred');
        }
        
        // Return the document with download headers
        return $this->response
            ->setHeader('Content-Type', $contentType ?: 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="document.pdf"')
            ->setBody($response);
    }

    public function returnDocument()
    {
        // Get JSON data from request
        $json = $this->request->getJSON();
        
        // Call backend API to return document
        $url = 'http://localhost:8080/api/document/return';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $this->response->setStatusCode($httpCode)->setBody($response);
    }
}
