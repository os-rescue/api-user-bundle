<?php

namespace API\UserBundle\Tests\Mailer;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Mailer\EmailTemplateRenderer;
use API\UserBundle\Mailer\EmailTemplateUrlGeneratorInterface;
use API\UserBundle\Model\UserInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EmailTemplateRendererTest extends TestCase
{
    private $eventDispatcher;
    private $templating;
    private $emailTemplateUrlGenerator;
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->emailTemplateUrlGenerator = $this->getMockBuilder(EmailTemplateUrlGeneratorInterface::class)
            ->getMock()
        ;
        $this->templating = $this->getMockBuilder(EngineInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
    }

    public function testRenderWithEmptyRoute(): void
    {
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(UserEvent::SENT_MAIL, $this->isInstanceOf(UserEvent::class))
        ;
        $this->user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn(null)
        ;
        $this->emailTemplateUrlGenerator
            ->expects($this->once())
            ->method('generateRoute')
            ->with(null, null)
            ->willReturn(null)
        ;
        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with('foo', ['user' => $this->user])
            ->willReturn('foo_content')
        ;

        $emailTemplateRenderer = new EmailTemplateRenderer(
            $this->eventDispatcher,
            $this->templating,
            $this->emailTemplateUrlGenerator
        )
        ;
        $emailTemplateRenderer->render($this->user, 'foo');
    }

    public function testRenderWithSettingRoute(): void
    {
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(UserEvent::SENT_MAIL, $this->isInstanceOf(UserEvent::class))
        ;
        $token = 'foobar';
        $this->user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn($token)
        ;
        $url = 'foobarfoo';
        $this->emailTemplateUrlGenerator
            ->expects($this->once())
            ->method('generateRoute')
            ->with('bar', $token)
            ->willReturn($url)
        ;
        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with('foo', ['user' => $this->user, 'confirmationUrl' => $url])
            ->willReturn('foo_content')
        ;

        $emailTemplateRenderer = new EmailTemplateRenderer(
            $this->eventDispatcher,
            $this->templating,
            $this->emailTemplateUrlGenerator
        )
        ;
        $emailTemplateRenderer->render($this->user, 'foo', 'bar');
    }

    public function testRenderWithAdditionRouteParams(): void
    {
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(UserEvent::SENT_MAIL, $this->isInstanceOf(UserEvent::class))
        ;
        $token = 'foobar';
        $this->user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn($token)
        ;
        $url = 'foobarfoo';
        $this->emailTemplateUrlGenerator
            ->expects($this->once())
            ->method('generateRoute')
            ->with('bar', $token)
            ->willReturn($url)
        ;
        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with('foo', ['user' => $this->user, 'confirmationUrl' => $url, 'param1' => 'value1'])
            ->willReturn('foo_content')
        ;

        $emailTemplateRenderer = new EmailTemplateRenderer(
            $this->eventDispatcher,
            $this->templating,
            $this->emailTemplateUrlGenerator
        )
        ;
        $emailTemplateRenderer->render($this->user, 'foo', 'bar', ['param1' => 'value1']);
    }
}
