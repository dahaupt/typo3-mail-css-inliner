<?php
declare(strict_types = 1);
namespace Pagemachine\MailCssInliner\Tests\Unit\Mail\Plugin;

/*
 * This file is part of the Pagemachine Mail CSS Inliner project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pagemachine\MailCssInliner\Mail\Plugin\CssInlinerPlugin;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Testcase for Pagemachine\MailCssInliner\Mail\Plugin\CssInlinerPlugin
 */
class CssInlinerPluginTest extends UnitTestCase
{
    /**
     * @var CssInlinerPlugin
     */
    protected $cssInlinerPlugin;

    /**
     * @var CssToInlineStyles|\Prophecy\Prophecy\ObjectProphecy
     */
    protected $converter;

    /**
     * Set up this testcase
     */
    public function setUp()
    {
        $this->converter = $this->prophesize(CssToInlineStyles::class);
        $this->cssInlinerPlugin = new CssInlinerPlugin($this->converter->reveal());
    }

    /**
     * @test
     */
    public function processesHtmlPart()
    {
        /** @var \Swift_Mime_MimePart|\Prophecy\Prophecy\ObjectProphecy */
        $message = $this->prophesize(\Swift_Mime_MimePart::class);
        $message->getContentType()->willReturn('text/html');
        $message->getBody()->willReturn('before');
        $message->setBody('after')->shouldBeCalled();
        $message->getChildren()->willReturn([]);

        /** @var \Swift_Events_SendEvent|\Prophecy\Prophecy\ObjectProphecy */
        $event = $this->prophesize(\Swift_Events_SendEvent::class);
        $event->getMessage()->willReturn($message->reveal());

        $this->converter->convert('before')->willReturn('after');

        $this->cssInlinerPlugin->beforeSendPerformed($event->reveal());
    }

    /**
     * @test
     */
    public function processesChildren()
    {
        /** @var \Swift_Mime_MimePart|\Prophecy\Prophecy\ObjectProphecy */
        $plainPart = $this->prophesize(\Swift_Mime_MimePart::class);
        $plainPart->getContentType()->willReturn('text/plain');
        $plainPart->getChildren()->willReturn([]);

        /** @var \Swift_Mime_MimePart|\Prophecy\Prophecy\ObjectProphecy */
        $htmlPart = $this->prophesize(\Swift_Mime_MimePart::class);
        $htmlPart->getContentType()->willReturn('text/html');
        $htmlPart->getBody()->willReturn('before');
        $htmlPart->setBody('after')->shouldBeCalled();
        $htmlPart->getChildren()->willReturn([]);

        /** @var \Swift_Mime_MimePart|\Prophecy\Prophecy\ObjectProphecy */
        $message = $this->prophesize(\Swift_Mime_MimePart::class);
        $message->getContentType()->willReturn('multipart/alternative');
        $message->getChildren()->willReturn([
            $plainPart->reveal(),
            $htmlPart->reveal(),
        ]);

        /** @var \Swift_Events_SendEvent|\Prophecy\Prophecy\ObjectProphecy */
        $event = $this->prophesize(\Swift_Events_SendEvent::class);
        $event->getMessage()->willReturn($message->reveal());

        $this->converter->convert('before')->willReturn('after');

        $this->cssInlinerPlugin->beforeSendPerformed($event->reveal());
    }
}
