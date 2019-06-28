<?php

namespace Charcoal\FilePond\Service\Helper;

require_once('Post.php');

/**
 * Class UniqueIdDispenser
 */
class UniqueIdDispenser {
    private static $counter = 0;
    public static function dispense() {
        return md5(uniqid(self::$counter++, true));
    }
}

/**
 * Class Transfer
 */
class Transfer {
    private $id;
    private $file;
    private $variants = [];
    private $metadata = [];
    public function __construct($id = false) {
        $this->id = $id ? $id : UniqueIdDispenser::dispense();
    }
    public function restore($file, $variants = [], $metadata = []) {
        $this->file = $file;
        $this->variants = $variants;
        $this->metadata = $metadata;
    }
    public function populate($entry) {
        $files = to_array_of_files($_FILES[$entry]);
        $metadata = isset($_POST[$entry]) ? to_array($_POST[$entry]) : [];
        // parse metadata
        if (count($metadata)) {
            $this->metadata = @json_decode($metadata[0]);
        }
        // files should always be available, first file is always the main file
        $this->file = $files[0];

        // if variants submitted, set to variants array
        $this->variants = array_slice($files, 1);
    }
    public function getid() {
        return $this->id;
    }
    public function getMetadata() {
        return $this->metadata;
    }
    public function getFiles($mutator = null) {
        $files = array_merge(isset($this->file) ? [$this->file] : [], $this->variants);
        return $mutator === null ? $files : call_user_func($mutator, $files, $this->metadata);
    }
}
