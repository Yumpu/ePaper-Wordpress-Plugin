<?php

class YumpuEpaper {
    private $id;
    private $progress_id;
    private $epaper_id;
    private $url;
    private $short_url;
    private $title;
    private $description;
    private $source_filename;
    private $embed_code;
    private $status;
    private $image_small;
    private $image_medium;
    private $image_big;
    private $privacy_mode;
    private $create_date;

    /**
     * @return the $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return the $embed_code
     */
    public function getEmbed_code() {
        return $this->embed_code;
    }

    /**
     * @param field_type $embed_code
     */
    public function setEmbed_code($embed_code) {
        $this->embed_code = $embed_code;
    }

    /**
     * @return the $progress_id
     */
    public function getProgress_id() {
        return $this->progress_id;
    }

    /**
     * @return the $epaper_id
     */
    public function getEpaper_id() {
        return $this->epaper_id;
    }

    /**
     * @return the $url
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return the $short_url
     */
    public function getShort_url() {
        return $this->short_url;
    }

    /**
     * @return the $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return the $description
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return the $source_filename
     */
    public function getSource_filename() {
        return $this->source_filename;
    }

    /**
     * @return the $status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @return the $Images
     */
    public function getImage_Small() {
        return $this->image_small;
    }

    public function getImage_Medium() {
        return $this->image_medium;
    }

    public function getImage_Big() {
        return $this->image_big;
    }

    /**
     * @return the $privacy_mode
     */
    public function getPrivacy_Mode() {
        return $this->privacy_mode;
    }

    /**
     * @return the $create_date
     */
    public function getCreate_Date() {
        return $this->create_date;
    }

    /**
     * @param field_type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @param field_type $progress_id
     */
    public function setProgress_id($progress_id) {
        $this->progress_id = $progress_id;
    }

    /**
     * @param field_type $epaper_id
     */
    public function setEpaper_id($epaper_id) {
        $this->epaper_id = $epaper_id;
    }

    /**
     * @param field_type $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * @param field_type $short_url
     */
    public function setShort_url($short_url) {
        $this->short_url = $short_url;
    }

    /**
     * @param field_type $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @param field_type $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @param field_type $source_filename
     */
    public function setSource_filename($source_filename) {
        $this->source_filename = $source_filename;
    }

    /**
     * @param field_type $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @param field_type $Image
     */
    public function setImage_Small($image) {
        $this->image_small = $image;
    }
    public function setImage_Medium($image) {
        $this->image_medium = $image;
    }
    public function setImage_Big($image) {
        $this->image_big = $image;
    }

    /**
     * @param field_type $create_date
     */
    public function setCreate_Date($create_date) {
        $this->create_date = $create_date;
    }
    /**
     * @param field_type $privacy_mode
     */
    public function setPrivacy_Mode($privacy_mode) {
        $this->privacy_mode = $privacy_mode;
    }

}

?>