<?php

namespace AppBundle\Twig;

/* In this file you can see our custom Twig extension that we need for our sitemap */
class WebsiteTwigExtension extends \Twig_Extension
{
    /**
     * Registers the Twig functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('app_sitemap', [$this, 'getSitemapStructure']),
        ];
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
            if ($child['template'] == 'artists') {
                array_push($result,$this->renderArtists($child));
            } elseif ($child['template'] == 'blog') {
                array_push($result,$this->renderBlog($child));
            } else {
                array_push($homeChilds,$child);
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

        foreach($artistsElem['children'] as $artist) {
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
     * Renders the Blog tree correctly.
     *
     * @param array $tree
     *
     * @return array
     */
    private function renderBlog($blog)
    {
        $result = $blog;
        $articles = [];

        foreach ($blog['children'] as $article) {
            array_push($articles, $article);
        }

        $result['articles'] = $this->splitIntoRows($articles, 3);

        return $result;
    }

    /**
     * Splits elements into Rows
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

            for ($i = 0; $i <= $rows; $i++) {
                $result[$i] = [];
            }

            foreach ($elements as $element) {
                array_push($result[$row], $element);
                $row++;
                $row = ($row > $rows ? 0 : $row);

            }
        } else {
            $result = array($elements);
        }

        return $result;
    }
}