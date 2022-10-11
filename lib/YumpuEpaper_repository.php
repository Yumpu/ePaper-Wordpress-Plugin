<?php

class YumpuEpaper_repository {

    public static function loadByAPI($mydata)
    {
        try {

            $ePaper = new YumpuEpaper();
            $ePaper->setEpaper_id($mydata->id);
            $ePaper->setTitle($mydata->title);
            $ePaper->setUrl($mydata->url);
            $ePaper->setShort_url($mydata->short_url);
            $ePaper->setDescription($mydata->description);
            $ePaper->setImage_Small($mydata->image->small);
            $ePaper->setImage_Medium($mydata->image->medium);
            $ePaper->setImage_Big($mydata->image->big);
            $ePaper->setEmbed_code($mydata->embed_code);
            $ePaper->setLanguage($mydata->language);
            $ePaper->setStatus('progress');
            $ePaper->setCreate_Date($mydata->create_date);
            $ePaper->setPrivacy_Mode($mydata->settings->privacy_mode);

            if ($ePaper->getStatus() == "progress") {
                if (basename($mydata->image->small) == "default.jpg") {
                    $ePaper->setStatus('ready');
                }
            }
            return $ePaper;
        }catch (Exception $e) {
            throw new YumpuEpaper_repository_exception($e->getMessage());
            __return_null();
        }
    }

    public static function LoadEpaperfromID($mydata)
    {
        try {
            $ePaper = new YumpuEpaper();
            $ePaper->setEpaper_id($mydata->document[0]->id);
            $ePaper->setTitle($mydata->document[0]->title);
            $ePaper->setUrl($mydata->document[0]->url);
            $ePaper->setShort_url($mydata->document[0]->short_url);
            $ePaper->setDescription($mydata->document[0]->description);
            $ePaper->setImage_Small($mydata->document[0]->image->small);
            $ePaper->setImage_Medium($mydata->document[0]->image->medium);
            $ePaper->setImage_Big($mydata->document[0]->image->big);
            $ePaper->setEmbed_code($mydata->document[0]->embed_code);
            $ePaper->setStatus('success');
            $ePaper->setCreate_Date($mydata->document[0]->create_date);
            $ePaper->setPrivacy_Mode($mydata->document[0]->settings->privacy_mode);

            return $ePaper;
        }catch (Exception $e) {
            throw new YumpuEpaper_repository_exception($e->getMessage());
            __return_null();
        }
    }

    /**
     * Liefert ein Array mit den YumpuEpapers
     */
    public static function getAll()
    {
        global $wpdb;

        $items = array();

        $YAPI = new YumpuAPI(WP_Yumpu::$API_TOKEN);
        $result = $YAPI->documents_get(0, 100, 'desc');
        if ($result) {
            foreach ($result->documents as $mydata) {
                $items[] = YumpuEpaper_repository::loadByAPI($mydata);
            }
        }
        return $items;
    }

    /**
     *
     * Erstellt ein neues Yumpu ePaper
     * @param string $file_to_import
     * @param string $title
     * @param description $description
     * @throws YumpuEpaper_repository_exception
     */
    public static function create($file_to_import, $title, $description = "") {
        if(strlen($title) < 5 || strlen($title) > 255) {
            throw new YumpuEpaper_repository_exception("need title - Min. length 5 characters, max. length 255 characters");
        }

        if(!empty($description) && strlen($description) < 5) {
            throw new YumpuEpaper_repository_exception("description to short - Min. length 5 characters, max. length 2500 characters");
        }

        if(!empty($description) && strlen($description) > 2500) {
            throw new YumpuEpaper_repository_exception("description to long - Min. length 5 characters, max. length 2500 characters");
        }

        if(empty($description)) {
            $description = null;
        }

        //Nun übertragen wir die Daten an YUMPU
        $YAPI = new YumpuAPI(WP_Yumpu::$API_TOKEN);
        try {
            $result = $YAPI->document_add($file_to_import, $title, $description);

            return $result->progress_id;

        } catch (YumpuAPI_Exception $e) {
            throw new YumpuEpaper_repository_exception(implode("<br>", $YAPI->get_errors()));
        }
    }
}

class YumpuEpaper_repository_exception extends Exception {}
