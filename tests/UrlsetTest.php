<?php


use Sitemaped\Element\Urlset\Image;
use Sitemaped\Element\Urlset\News;
use Sitemaped\Element\Urlset\Url;
use Sitemaped\Element\Urlset\Urlset;
use Sitemaped\Element\Urlset\Video;
use Sitemaped\Sitemap;

class UrlsetTest extends \PHPUnit\Framework\TestCase
{
    /** @var Sitemap */
    private $sitemap;

    public function setUp() : void
    {
        parent::setUp();
        $urlset = new Urlset();
        foreach (range(1, 2) as $i) {
            $url = new Url(
                'https://test.com/'.$i,
                new \DateTime(),
                Url::CHANGEFREQ_MONTHLY,
                1
            );

            $url->addImage(new Image('https://test.com/image/'.$i));
            $url->addVideo(new Video('https://test.com/video/'.$i, 'Title '.$i, 'Description '.$i));
            $url->addNews(new News('Awesome news '.$i, '2018-01-01', 'Awesome news name '.$i, 'ru-RU'));

            $urlset->addUrl($url);
        }

        $sitemap = new Sitemap($urlset);

        $this->sitemap = $sitemap;
    }

    public function testAlternateLinks()
    {
        $urlset = new Urlset();
        foreach (range(1, 2) as $i) {
            $url = new Url(
                'https://test.com/'.$i,
                new \DateTime(),
                Url::CHANGEFREQ_MONTHLY,
                1
            );

            $url->addImage(new Image('https://test.com/image/'.$i));
            $url->addVideo(new Video('https://test.com/video/'.$i, 'Title '.$i, 'Description '.$i));
            $url->addNews(new News('Awesome news '.$i, '2018-01-01', 'Awesome news name '.$i, 'ru-RU'));
            $url->addAlternate('https://test.com/image/de/'.$i, 'de');

            $urlset->addUrl($url);
        }

        $content = (new Sitemap($urlset))->toXmlString();
        $this->assertStringContainsString('xhtml:link', $content);
        $this->assertStringContainsString('http://www.w3.org/1999/xhtml', $content);
    }

    public function testXmlOutput()
    {
        $content = (string) $this->sitemap;
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('urlset', $content);
        $this->assertStringContainsString('video:video', $content);
    }

    public function testTxtOutput()
    {
        $content = $this->sitemap->toTxtString();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('https://test.com/1', $content);
        $this->assertStringNotContainsString('https://test.com/image/1', $content);
    }

    public function testGzipCompression()
    {
        $content = $this->sitemap->toXmlString(true);
        $content = gzdecode($content);
        $this->assertStringContainsString('urlset', $content);
        $this->assertStringContainsString('video:video', $content);
        $this->assertStringContainsString('https://test.com/1', $content);
    }

}
