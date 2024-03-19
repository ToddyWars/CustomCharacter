<?php

namespace custom\utils;

use custom\CharLoader;
use GdImage;
use JsonException;
use pocketmine\entity\Skin;
use pocketmine\player\Player;

class SkinUtils
{
    private static function getGeometryIdFromJsonFile(array $json): ?string {
        $geometryId = null;
        if (isset($json["minecraft:geometry"])) {
            foreach ($json["minecraft:geometry"] as $ji) {
                if (isset($ji["description"]) and isset($ji["description"]["identifier"])) {
                    $geometryId = $ji["description"]["identifier"];
                }
            }
        }
        return $geometryId;
    }

    /**
     * @throws JsonException
     */
    static function getSkinFromPath(String $locate, String $gpath, String $gid, String $png) : ?Skin
    {
        $path = CharLoader::getPlugin()->getDataFolder() . "corpos/$png.png";

        $size = getimagesize($path);

        $path = self::imgTricky($path, $locate, [$size[0], $size[1], 4]);
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        for ($y = 0; $y < $size[1]; $y++) {
            for ($x = 0; $x < $size[0]; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~($colorat >> 24)) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);

        return new Skin($gid, $skinbytes, '', $gid, $gpath);
    }

    /**
     * @throws JsonException
     */
    static function changeSkin(Player $player, String $cabelo, String $corpo):void
    {
        $folder = CharLoader::getPlugin()->getDataFolder();

        $data  = json_decode(file_get_contents($folder."/cabelos/$cabelo.json"), true);
        $data2 = json_decode(file_get_contents($folder."/corpos/$corpo.json"), true);

        $hair = $data['minecraft:geometry'][0]['bones'];
        foreach($hair as $index => $key){
            if($index > 4)
                $data2['minecraft:geometry'][0]['bones'][] = $key;
        }
        $gid = self::getGeometryIdFromJsonFile($data2);
        $skin = SkinUtils::getSkinFromPath($folder."/cabelos/hair.png", json_encode($data2), $gid, $corpo);
        $player->setSkin($skin);
        $player->sendSkin();
    }

    private static function imgTricky(string $skinPath, string $locate, array $size): string
    {
        $path = CharLoader::getPlugin()->getDataFolder();
        $down = imagecreatefrompng($skinPath);
        if ($size[0] * $size[1] * $size[2] == 65536) {
            $upper = self::resize_image($locate, 128, 128);
        } else {
            $upper = self::resize_image($locate, 64, 64);
        }
        //Remove black color out of the png
        imagecolortransparent($upper, imagecolorallocatealpha($upper, 0, 0, 0, 127));

        imagealphablending($down, true);
        imagesavealpha($down, true);
        imagecopymerge($down, $upper, 0, 0, 0, 0, $size[0], $size[1], 100);
        imagepng($down, $path . 'temp.png');
        return CharLoader::getPlugin()->getDataFolder() . 'temp.png';

    }

    private static function resize_image($file, $w, $h, $crop = false): GdImage|bool
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }
}