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

	
	
}

?>