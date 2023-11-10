<?php

namespace Joomla\Module\AnirataFiles\Site;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;

class ModAnirataHelper {

    public static function humanFilesize($bytes, $dec = 2): string {
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor == 0) $dec = 0;

        return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
    }

    public static function getFiles($params) {

        $timezone = date_default_timezone_get();
        $user = Factory::getUser();
        if ($user != null) $timezone = $user->getTimezone();

        $res = [];

        $files_path = JPATH_BASE . '/' . $params->get('files_path');

        $files = array_merge(glob($files_path . '/*'), glob($files_path . '/**/*'));

        foreach($files as $i => $file)
        {
            if (\is_dir($file)) continue;

            $name = substr($file, strlen($files_path) + 1);
            $url = Uri::base() . htmlentities(substr($file, strlen(JPATH_BASE) + 1 ));
            $urlEncoded = urlencode($url);

            $size = filesize($file);
            $rsize = self::humanFilesize($size);

            $created = new \DateTime(date('c',filectime($file)));
            $modified = new \DateTime(date('c',filemtime($file)));

            $created->setTimezone($timezone);
            $modified->setTimezone($timezone);

			$extention = pathinfo($file, PATHINFO_EXTENSION);
			$isImage = in_array(strtolower($extention), ['bmp','gif','jpg','png','jpeg','webp','svg']);
			
            $res[] = [
                'file' => $file,
                'name' => $name,
				'extention' => $extention,
                'url' => $url,
                'urlEncoded' => $urlEncoded,
                'size' => $size,
                'rsize' => $rsize,
                'created_date' => $created,
                'modified_date' => $modified,
				'j_created_date' => new Date($created->format('c')),
				'j_modified_date' => new Date($modified->format('c')),
				'is_image' => $isImage
            ];
        }

        return $res;
    }
}

