<?php

namespace Simple\Asset;

use MatthiasMullie\Minify;

/**
 * wrapper for MatthiasMullie\Minify
 */
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
                @mkdir($dir, 0755, true);
            }
        }

        $this->path = $path;

        $this->opts = array_merge($this->opts, $opts);
    }

    /**
     * css loader
     *
     * @param array $files
     * @return string
     */
    public function css(array $files)
    {
        $minify = new Minify\CSS();
        $minify->setMaxImportSize(2048);

        $minify->setImportExtensions(
            array(
                'gif' => 'data:image/gif',
                'png' => 'data:image/png',
                'jpe' => 'data:image/jpeg',
                'jpg' => 'data:image/jpeg',
                'jpeg' => 'data:image/jpeg',
                'svg' => 'data:image/svg+xml',
                'woff' => 'data:application/x-font-woff',
                'ttf' => 'data:application/x-font-ttf',
                'ttc' => 'data:application/x-font-ttf',
                'otf' => 'data:application/x-font-otf',
                'eot' => 'data:application/vnd.ms-fontobject',
                'woff2' => 'data:application/font-woff2',
                'tif' => 'image/tiff',
                'tiff' => 'image/tiff',
                'xbm' => 'image/x-xbitmap',
            )
        );

        $minify->add($files);

        return $this->offer(
            $minify,
            $files,
            array(
                'Content-type' => 'text/css'
            )
        );
    }

    /**
     * js loader
     *
     * @param array $files
     * @return string
     */
    public function js(array $files)
    {
        $minify = new Minify\JS();

        return $this->offer(
            $minify,
            $files,
            array(
                'Content-type' => 'text/javascript'
            )
        );
    }
    
    /**
     * offer
     *
     * @param Minify\Minify $minify
     * @param array $files
     * @param array $header
     * @return string
     */
    private function offer($minify, $files, $header = array())
    {
        header("Expires: ".gmdate('D, d M Y H:i:s', time() + $this->opts['timeout'])." GMT", true);
        header("Last-Modified: ".gmdate('D, d M Y H:i:s', time())." GMT", true);
        header("Cache-Control: max-age=".$this->opts['maxAge'], true);
        header("Pragma: cache", true);

        $minify->add($files);

        $buffer = $this->opts['gzip']
            ? $minify->gzip(
                $this->path,
                (int) $this->opts['gzip']
            )
            : $minify->minify(
                $this->path
            );

        foreach ($header as $key => $value) {
            header("$key: $value", true);
        }

        if ($this->opts['etag']) {
            header("Etag: " . md5($buffer), true);
        }
        session_cache_limiter('public');

        return $buffer;
    }
}
