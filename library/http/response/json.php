<?php
/**
 */

class lib_http_response_json extends \Symfony\Component\HttpFoundation\JsonResponse {

	use lib_http_response_trait;
    protected function update() {
        $this->headers->set('Content-Type', 'application/json;charset=utf-8');
        return parent::update();
        
    }

}

