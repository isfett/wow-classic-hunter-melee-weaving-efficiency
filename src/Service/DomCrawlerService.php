<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Service;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class DomCrawlerService
 */
class DomCrawlerService
{
    /** @var Crawler */
    private $domCrawler;

    /**
     * DomCrawlerService constructor.
     *
     * @param Crawler|null $domCrawler
     */
    public function __construct(?Crawler $domCrawler = null)
    {
        $this->domCrawler = $domCrawler;
        if (null === $this->domCrawler) {
            $this->domCrawler = new Crawler();
        }
    }

    /**
     * @param string $html
     * @param string $filter
     *
     * @return int
     */
    public function countFilteredNodes(string $html, string $filter): int
    {
        $this->domCrawler->clear();
        $this->domCrawler->addHtmlContent($html);

        return count($this->domCrawler->filter($filter));
    }
}
