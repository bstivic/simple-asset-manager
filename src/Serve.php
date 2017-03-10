<?php

namespace App;

use MatthiasMullie\Minify;

class Serve
{
    public $path;
    public $opts = array(
                      'etag' => true,
                      'timeout' => 604800,
                      'maxAge' => 315360000,
                      'gzip' => false
                      );
    
    
    public function __construct($path = null, $opts = array())
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
        
        if (!empty($opts)) {
            $this->opts = array_merge($this->opts, $opts);
        }
    }
    
    public function css(array $files)
    {
        header('Content-type: text/css');
        $minify = new Minify\CSS();
        
        return $this->offer($minify, $files);
    }
    
    public function js(array $files)
    {
        header('Content-type: text/javascript');
        $minify = new Minify\JS();
        
        return $this->offer($minify, $files);
    }
    
    private function offer($minify, $files)
    {
        $minify->add($files);
        
        $buffer = $this->opts['gzip'] ? $minify->gzip($this->path, (int) $this->opts['gzip']) : $minify->minify($this->path);
        
        $this->http_header();
        if ($this->opts['etag']) {
            header("Etag: " . md5($buffer));
        }
        
        return $buffer;
    }
    
    private function http_header()
    {
        header("Expires: ". gmdate('D, d M Y H:i:s', time() + $this->opts['timeout']) ." GMT");
        header("Last-Modified: ". gmdate('D, d M Y H:i:s', time()) ." GMT");
        header("Cache-Control: max-age=" . $this->opts['maxAge']);
        header("Pragma: cache");
        session_cache_limiter('public');
    }
}
