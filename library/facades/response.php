<?php
/**
 * response.php
 *
 * */
class lib_facades_response extends lib_facades_facade
{
    use lib_support_traits_macro;
    /**
     * Return the Response instance
     *
     * @var \Symfony\Component\HttpFoundation\Response;
     */
    private static $__response;
    
    //	use MacroableTrait;
    
    /**
     * Return a new response from the application.
     *
     * @param  string  $content
     * @param  int     $status
     * @param  array   $headers
     * @return \Illuminate\Http\Response
     */
    public static function make($content = '', $status = 200, array $headers = array())
    {
        return new lib_http_response($content, $status, $headers);
    }
    
    /**
     * Return a new JSON response from the application.
     *
     * @param  string|array  $data
     * @param  int    $status
     * @param  array  $headers
     * @param  int    $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function json($data = array(), $status = 200, array $headers = array(), $options = 0)
    {
        return new lib_http_response_json($data, $status, $headers, $options);
    }
   
}
