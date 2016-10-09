<?php
/**
 */


class lib_http_response_redirect extends \Symfony\Component\HttpFoundation\RedirectResponse {

    use lib_http_response_trait;

	/**
	 * The request instance.
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Get the request instance.
	 *
	 * @return  \Illuminate\Http\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Set the request instance.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}
    
    
}
