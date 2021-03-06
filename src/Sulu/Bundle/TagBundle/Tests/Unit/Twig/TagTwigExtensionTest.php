<?php
/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\TagBundle\Tests\Unit\Twig;

use Sulu\Bundle\TagBundle\Entity\Tag;
use Sulu\Bundle\TagBundle\Tag\TagManagerInterface;
use Sulu\Bundle\TagBundle\Twig\TagTwigExtension;
use Sulu\Component\Tag\Request\TagRequestHandler;
use Sulu\Component\Tag\Request\TagRequestHandlerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class TagTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function getProvider()
    {
        return [
            [[]],
            [['sulu']],
            [['sulu', 'core']],
            [['sulu', 'core', 'massive art']],
        ];
    }

    /**
     * @dataProvider getProvider
     */
    public function testGetTags($tagNames)
    {
        $tags = [];
        foreach ($tagNames as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName);

            $tags[] = $tag;
        }

        $tagManager = $this->prophesize(TagManagerInterface::class);
        $tagManager->findAll()->shouldBeCalled()->willReturn($tags);

        $tagRequestHandler = $this->prophesize(TagRequestHandlerInterface::class);

        $tagExtension = new TagTwigExtension($tagManager->reveal(), $tagRequestHandler->reveal());
        $this->assertEquals($tags, $tagExtension->getTagsFunction());
    }

    public function appendProvider()
    {
        return [
            ['t', '/test', 'Sulu,Core', 'Sulu,Core,Test'],
            ['tags', '/asdf', 'Sulu,Core', 'Sulu,Core,Test'],
            ['t', '/asdf', 'Sulu,Core', 'Sulu,Core,Test'],
            ['tags', '/test', 'Sulu,Core', 'Sulu,Core,Test'],
            ['tags', '/test', 'Sulu,Test', 'Sulu,Test'],
            ['tags', '/test', '', 'Test'],
        ];
    }

    /**
     * @dataProvider appendProvider
     */
    public function testAppendTagUrl($tagsParameter, $url, $tagsString, $expected)
    {
        $tag = new Tag();
        $tag->setName('Test');

        $tagManager = $this->prophesize(TagManagerInterface::class);
        $requestStack = $this->prophesize(RequestStack::class);
        $request = $this->prophesize(Request::class);

        $requestReveal = $request->reveal();
        $requestReveal->query = new ParameterBag([$tagsParameter => $tagsString]);
        $requestStack->getCurrentRequest()->willReturn($requestReveal);
        $request->get($tagsParameter, '')->willReturn($tagsString);
        $request->getPathInfo()->willReturn($url);

        $tagRequestHandler = new TagRequestHandler($requestStack->reveal());

        $tagExtension = new TagTwigExtension($tagManager->reveal(), $tagRequestHandler);
        $result = $tagExtension->appendTagUrlFunction($tag, $tagsParameter);

        $this->assertEquals($url . '?' . $tagsParameter . '=' . urlencode($expected), $result);
    }

    public function setProvider()
    {
        return [
            ['t', '/test', 'Sulu,Core', 'Test'],
            ['tags', '/asdf', 'Sulu,Core', 'Test'],
            ['t', '/asdf', 'Sulu,Core', 'Test'],
            ['tags', '/test', 'Sulu,Core', 'Test'],
            ['tags', '/test', 'Sulu,Test', 'Test'],
            ['tags', '/test', '', 'Test'],
        ];
    }

    /**
     * @dataProvider setProvider
     */
    public function testSetTagUrl($tagsParameter, $url, $tagsString, $expected)
    {
        $tag = new Tag();
        $tag->setName('Test');

        $tagManager = $this->prophesize(TagManagerInterface::class);
        $requestStack = $this->prophesize(RequestStack::class);
        $request = $this->prophesize(Request::class);

        $requestReveal = $request->reveal();
        $requestReveal->query = new ParameterBag([$tagsParameter => $tagsString]);
        $requestStack->getCurrentRequest()->willReturn($requestReveal);
        $request->get($tagsParameter, '')->willReturn($tagsString);
        $request->getPathInfo()->willReturn($url);

        $tagRequestHandler = new TagRequestHandler($requestStack->reveal());

        $tagExtension = new TagTwigExtension($tagManager->reveal(), $tagRequestHandler);
        $result = $tagExtension->setTagUrlFunction($tag, $tagsParameter);

        $this->assertEquals($url . '?' . $tagsParameter . '=' . urlencode($expected), $result);
    }

    public function clearProvider()
    {
        return [
            ['t', '/test', 'Sulu,Core'],
            ['t', '/asdf', 'Sulu,Core'],
            ['tags', '/asdf', 'Sulu,Core'],
            ['tags', '/test', 'Sulu,Core'],
            ['tags', '/test', 'Sulu,Test'],
            ['tags', '/test', ''],
        ];
    }

    /**
     * @dataProvider clearProvider
     */
    public function testClearTagUrl($tagsParameter, $url, $tagsString)
    {
        $tag = new Tag();
        $tag->setName('Test');

        $tagManager = $this->prophesize(TagManagerInterface::class);
        $requestStack = $this->prophesize(RequestStack::class);
        $request = $this->prophesize(Request::class);

        $requestReveal = $request->reveal();
        $requestReveal->query = new ParameterBag([$tagsParameter => $tagsString]);
        $requestStack->getCurrentRequest()->willReturn($requestReveal);
        $request->get($tagsParameter, '')->willReturn($tagsString);
        $request->getPathInfo()->willReturn($url);

        $tagRequestHandler = new TagRequestHandler($requestStack->reveal());

        $tagExtension = new TagTwigExtension($tagManager->reveal(), $tagRequestHandler);
        $result = $tagExtension->clearTagUrlFunction($tagsParameter);

        $this->assertEquals($url, $result);
    }
}
