<?php

namespace API\UserBundle\Tests\Mailer;

use API\UserBundle\Mailer\EmailTemplateUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailTemplateUrlGeneratorTest extends TestCase
{
    private $router;

    protected function setUp()
    {
        parent::setUp();

        $this->router = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();
    }

    public function testGenerateUrlWithEmptyRoute(): void
    {
        $this->router
            ->expects($this->never())
            ->method('generate')
        ;

        $emailTemplateUrlGenerator = new EmailTemplateUrlGenerator($this->router);
        $url = $emailTemplateUrlGenerator->generateRoute(null, 'foo');

        $this->assertNull($url);
    }

    public function testGenerateUrlWithSettingRoute(): void
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with(
                'bar',
                ['token' => 'foo'],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn('foobarfoo')
        ;

        $emailTemplateUrlGenerator = new EmailTemplateUrlGenerator($this->router);
        $emailTemplateUrlGenerator->generateRoute('bar', 'foo');
    }
}
