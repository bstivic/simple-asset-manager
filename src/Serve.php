<?php

namespace App;

use MatthiasMullie\Minify;

class Serve
{
    var $path;
    
    public function __construct($path = null)
    {
        if (!empty($path)) {
            $dir = dirname($path);
            if (!file_exists($dir)) {
                
                if (@mkdir($dir, 0755, true)) {
                    $this->path = $path;
                }
                
            } else {
                $this->path = $path;
            }
        }
    }
    
    public function css(array $files)
    {
        header('Content-type: text/css');
        $minify = new Minify\CSS();
        $minify->add($files);
        return $minify->minify($this->path);
    }
    
    public function js(array $files)
    {
        header('Content-type: text/javascript');
        $minify = new Minify\JS();
        $minify->add($files);
        
        return $minify->minify($this->path);
    }
    
    public function http_header($timeout = 604800, $maxAge = 315360000)
    {
        header("Expires: ". gmdate('D, d M Y H:i:s', time() + $timeout) ." GMT");
        header("Last-Modified: ". gmdate('D, d M Y H:i:s', time() ) ." GMT");
        header("Cache-Control: max-age=$maxAge");
        header("Pragma: cache");
        session_cache_limiter('public');
    }
}