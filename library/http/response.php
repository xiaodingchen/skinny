<?php
use lib_support_contracts_interface_renderable as RenderableInterface;
use Symfony\Component\HttpFoundation\Response as SysmfonyResponse;

class lib_http_response extends SysmfonyResponse {
    use lib_http_response_trait;
    /**
     * The original content of the response.
     *
     * @var mixed
     */
    public $original;
    
    /**
     * Set the content on the response.
     *
     * @param  mixed  $content
     * @return void
     */
    public function setContent($content)
    {
        $this->original = $content;
        // 不做json兼容. 所有操作要显性化.
    
        // If this content implements the "RenderableInterface", then we will call the
        // render method on the object so we will avoid any "__toString" exceptions
        // that might be thrown and have their errors obscured by PHP's handling.
        if ($content instanceof RenderableInterface)
        {
            $content = $content->render();
        }
    
        return parent::setContent($content);
    }
    
    /**
     * Get the original response content.
     *
     * @return mixed
     */
    public function getOriginalContent()
    {
        return $this->original;
    }
}