<?php

namespace AppBundle\Twig;

/**
 * Twig extension for rendering the Sitemap.
 */
class WebsiteTwigExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('app_sitemap', [$this, 'getSitemapStructure']),
            new \Twig_SimpleFunction('app_picture', [$this, 'getPictureTag']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_website';
    }

    /**
     * Returns a picture tag with the given breakpoints.
     *
     * @param string $defaultImageUrl
     * @param string $title
     * @param array $breakpoints
     * @param string $class
     *
     * @return string
     */
    public function getPictureTag($defaultImageUrl, $title, $breakpoints, $class = '')
    {
        $breakpointSources = '';

        if (!empty($breakpoints)) {
            foreach ($breakpoints as $breakpoint => $imageUrl) {
                $media = $breakpoint ? 'media=" ' . $breakpoint . ' "' : '';
                $path = ' srcset="' . $imageUrl[0] . ($imageUrl[1] ? ', ' . $imageUrl[1] . ' 2x' : '') . '"';
                $breakpointSources .= '<source' . $media . $path . '">';
            }
        }

        if ($class) {
            $class = ' class= "' . $class . '"';
        }

        return '<picture>
                    ' . $breakpointSources . '
                    <img' . $class . ' src="' . $defaultImageUrl . '" alt="' . $title . '"/>
                </picture>';
    }

    /**
     * Returns the correctly structured Sitemap.
     *
     * @param array $tree
     *
     * @return array
     */
    public function getSitemapStructure($tree)
    {
        $result = [];
        $home = $tree;
        $homeChilds = [];

        foreach ($tree['children'] as $child) {
            if ('artists' == $child['template']) {
                array_push($result, $this->renderArtists($child));
            } else {
                array_push($homeChilds, $child);
            }
        }

        $home['rows'] = $this->splitIntoRows($homeChilds, 3);
        array_unshift($result, $home);

        return $result;
    }

    /**
     * Renders the Artists tree correctly.
     *
     * @param array $artistsElem
     *
     * @return array
     */
    private function renderArtists($artistsElem)
    {
        $result = $artistsElem;
        $artists = [];
        $disks = [];

        foreach ($artistsElem['children'] as $artist) {
            array_push($artists, $artist);

            foreach ($artist['children'] as $disk) {
                array_push($disks, $disk);
            }
        }

        $result['artists'] = $this->splitIntoRows($artists, 1);
        $result['disks'] = $this->splitIntoRows($disks, 2);

        return $result;
    }

    /**
     * Splits elements into Rows.
     *
     * @param array $elements
     * @param int $maxRows
     *
     * @return array
     */
    private function splitIntoRows($elements, $maxRows)
    {
        $result = [];

        if (count($elements) > 5 && $maxRows > 1) {
            $rows = (count($elements) > 10 && $maxRows >= 3 ? 2 : 1);
            $row = 0;

            for ($i = 0; $i <= $rows; ++$i) {
                $result[$i] = [];
            }

            foreach ($elements as $element) {
                array_push($result[$row], $element);
                ++$row;
                $row = ($row > $rows ? 0 : $row);
            }
        } else {
            $result = [$elements];
        }

        return $result;
    }
}
