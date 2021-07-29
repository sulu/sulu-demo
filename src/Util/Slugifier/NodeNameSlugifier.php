<?php
/**
 * Created by Dmitry Prokopenko
 * https://github.com/dmitry-pro
 * Date: 2/20/18
 * Time: 15:07
 */

namespace App\Util\Slugifier;

use Symfony\Cmf\Api\Slugifier\SlugifierInterface;

/**
 * Wraps default slugifier and add some additional node-name stuff.
 */
class NodeNameSlugifier implements SlugifierInterface
{
    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * @param SlugifierInterface $slugifier
     */
    public function __construct(SlugifierInterface $slugifier)
    {
        $this->slugifier = $slugifier;
    }

    /**
     * @param string $text
     *
     * @return null|string
     */
    public static function cyrToLat($text)
    {
        $iso9Table = [
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G`',
            'Ґ' => 'G`', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE',
            'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'Y',
            'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K',
            'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS',
            'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '``',
            'Ы' => 'YI', 'Ь' => '`', 'Э' => 'E`', 'Ю' => 'YU', 'Я' => 'YA',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
            'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
            'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'y',
            'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k',
            'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
            'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'shh', 'ь' => '',
            'ы' => 'yi', 'ъ' => "'", 'э' => 'e`', 'ю' => 'yu', 'я' => 'ya',
        ];

        $text = strtr($text, $iso9Table);
        $text = preg_replace("/[^A-Za-z0-9`'_\-\.]/", '-', $text);

        return $text;
    }

    /**
     * Slugifies given string to a valid node-name.
     *
     * @param string $text
     *
     * @return string
     */
    public function slugify($text)
    {
        $text = self::cyrToLat($text);

        $text = $this->slugifier->slugify($text);

        // jackrabbit can not handle node-names which contains a number followed by "e" e.g. 10e
        $text = preg_replace('((\d+)([eE]))', '$1-$2', $text);

        return $text;
    }
}
