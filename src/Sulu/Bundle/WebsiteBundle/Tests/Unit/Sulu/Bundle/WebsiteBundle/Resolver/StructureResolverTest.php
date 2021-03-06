<?php

/*
 * This file is part of the Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\WebsiteBundle\Resolver;

use Prophecy\Argument;
use Sulu\Component\Content\Compat\StructureManagerInterface;
use Sulu\Component\Content\ContentTypeInterface;
use Sulu\Component\Content\ContentTypeManagerInterface;
use Sulu\Component\Content\Document\Extension\ExtensionContainer;

class StructureResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StructureResolverInterface
     */
    private $structureResolver;

    /**
     * @var ContentTypeManagerInterface
     */
    private $contentTypeManager;

    /**
     * @var ContentTypeInterface
     */
    private $contentType;

    /**
     * @var StructureManagerInterface
     */
    private $structureManager;

    public function setUp()
    {
        parent::setUp();

        $this->contentTypeManager = $this->prophesize('Sulu\Component\Content\ContentTypeManagerInterface');
        $this->structureManager = $this->prophesize('Sulu\Component\Content\Compat\StructureManagerInterface');
        $this->contentType = $this->prophesize('Sulu\Component\Content\ContentTypeInterface');

        $this->structureResolver = new StructureResolver(
            $this->contentTypeManager->reveal(),
            $this->structureManager->reveal()
        );
    }

    public function testResolve()
    {
        $this->contentTypeManager->get('content_type')->willReturn($this->contentType);

        $this->contentType->getViewData(Argument::any())->willReturn('view');
        $this->contentType->getContentData(Argument::any())->willReturn('content');

        $excerptExtension = $this->prophesize('Sulu\Component\Content\Extension\ExtensionInterface');
        $excerptExtension->getContentData(['test1' => 'test1'])->willReturn(['test1' => 'test1']);
        $this->structureManager->getExtension('test', 'excerpt')->willReturn($excerptExtension);

        $property = $this->prophesize('Sulu\Component\Content\Compat\PropertyInterface');
        $property->getName()->willReturn('property');
        $property->getContentTypeName()->willReturn('content_type');

        $structure = $this->prophesize('Sulu\Component\Content\Compat\Structure\PageBridge');
        $structure->getKey()->willReturn('test');
        $structure->getExt()->willReturn(new ExtensionContainer(['excerpt' => ['test1' => 'test1']]));
        $structure->getUuid()->willReturn('some-uuid');
        $structure->getProperties(true)->willReturn([$property->reveal()]);
        $structure->getCreator()->willReturn(1);
        $structure->getChanger()->willReturn(1);
        $structure->getCreated()->willReturn('date');
        $structure->getChanged()->willReturn('date');
        $structure->getPublished()->willReturn('date');
        $structure->getPath()->willReturn('test-path');
        $structure->getUrls()->willReturn(['en' => '/description', 'de' => '/beschreibung', 'es' => null]);
        $structure->getShadowBaseLanguage()->willReturn('en');

        $expected = [
            'extension' => [
                'excerpt' => ['test1' => 'test1'],
            ],
            'uuid' => 'some-uuid',
            'view' => [
                'property' => 'view',
            ],
            'content' => [
                'property' => 'content',
            ],
            'creator' => 1,
            'changer' => 1,
            'created' => 'date',
            'changed' => 'date',
            'published' => 'date',
            'template' => 'test',
            'urls' => ['en' => '/description', 'de' => '/beschreibung', 'es' => null],
            'path' => 'test-path',
            'shadowBaseLocale' => 'en',
        ];

        $this->assertEquals($expected, $this->structureResolver->resolve($structure->reveal()));
    }
}
