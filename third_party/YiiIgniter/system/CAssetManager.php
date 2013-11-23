<?php

class CAssetManager {

    const DEFAULT_BASEPATH = 'yw_assets';

    public $linkAssets = false;
    public $excludeFiles = array('.svn', '.gitignore');
    public $newFileMode = 0666;
    public $newDirMode = 0777;
    public $forceCopy = false;
    private $_basePath;
    private $_baseUrl;
    private $_published = array();

    public function getBasePath() {
        if ($this->_basePath === null) {
            $this->setBasePath(FCPATH . self::DEFAULT_BASEPATH);
        }
        return $this->_basePath;
    }

    public function setBasePath($value) {
        if (!is_dir($value)) {
            mkdir(FCPATH . self::DEFAULT_BASEPATH, $this->newDirMode, true);
            @chmod(FCPATH . self::DEFAULT_BASEPATH, $this->newDirMode);
        }

        if (($basePath = realpath($value)) !== false && is_dir($basePath) && is_writable($basePath)) {
            $this->_basePath = $basePath;
        } else {
            show_error('CAssetManager.basePath "' . $value . '" is invalid. Please make sure the directory exists and is writable by the Web server process.');
        }
    }

    public function getBaseUrl() {
        if ($this->_baseUrl === null) {
            $this->setBaseUrl(Yii::app()->baseUrl . self::DEFAULT_BASEPATH);
        }
        return $this->_baseUrl;
    }

    public function setBaseUrl($value) {
        $this->_baseUrl = rtrim($value, '/');
    }

    public function publish($path, $hashByName = false, $level = -1, $forceCopy = null) {
        if ($forceCopy === null)
            $forceCopy = $this->forceCopy;
        if ($forceCopy && $this->linkAssets)
            show_error('The "forceCopy" and "linkAssets" cannot be both true.');
        if (isset($this->_published[$path]))
            return $this->_published[$path];
        elseif (($src = realpath($path)) !== false) {
            $dir = $this->generatePath($src, $hashByName);
            $dstDir = $this->getBasePath() . DIRECTORY_SEPARATOR . $dir;
            if (is_file($src)) {
                $fileName = basename($src);
                $dstFile = $dstDir . DIRECTORY_SEPARATOR . $fileName;

                if (!is_dir($dstDir)) {
                    mkdir($dstDir, $this->newDirMode, true);
                    @chmod($dstDir, $this->newDirMode);
                }

                if ($this->linkAssets && !is_file($dstFile))
                    symlink($src, $dstFile);
                elseif (@filemtime($dstFile) < @filemtime($src)) {
                    copy($src, $dstFile);
                    @chmod($dstFile, $this->newFileMode);
                }

                return $this->_published[$path] = $this->getBaseUrl() . "/$dir/$fileName";
            } elseif (is_dir($src)) {
                if ($this->linkAssets && !is_dir($dstDir)) {
                    symlink($src, $dstDir);
                } elseif (!is_dir($dstDir) || $forceCopy) {
                    CFileHelper::copyDirectory($src, $dstDir, array(
                        'exclude' => $this->excludeFiles,
                        'level' => $level,
                        'newDirMode' => $this->newDirMode,
                        'newFileMode' => $this->newFileMode,
                    ));
                }

                return $this->_published[$path] = $this->getBaseUrl() . '/' . $dir;
            }
        }
        show_error('The asset "' . $path . '" to be published does not exist.');
    }

    public function getPublishedPath($path, $hashByName = false) {
        if (($path = realpath($path)) !== false) {
            $base = $this->getBasePath() . DIRECTORY_SEPARATOR . $this->generatePath($path, $hashByName);
            return is_file($path) ? $base . DIRECTORY_SEPARATOR . basename($path) : $base;
        }
        else
            return false;
    }

    public function getPublishedUrl($path, $hashByName = false) {
        if (isset($this->_published[$path]))
            return $this->_published[$path];
        if (($path = realpath($path)) !== false) {
            $base = $this->getBaseUrl() . '/' . $this->generatePath($path, $hashByName);
            return is_file($path) ? $base . '/' . basename($path) : $base;
        }
        else
            return false;
    }

    protected function hash($path) {
        return sprintf('%x', crc32($path . Yii::getVersion()));
    }

    protected function generatePath($file, $hashByName = false) {
        if (is_file($file))
            $pathForHashing = $hashByName ? basename($file) : dirname($file) . filemtime($file);
        else
            $pathForHashing = $hashByName ? basename($file) : $file . filemtime($file);

        return $this->hash($pathForHashing);
    }

}