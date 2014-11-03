<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Content\Mapper;

use Sulu\Component\Content\Structure;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Sulu\Component\Content\StructureInterface;
use PHPCR\PropertyType;

class ContentMapperSnippetTest extends SuluTestCase
{
    /**
     * @var ContentMapper
     */
    private $contentMapper;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Snippet
     */
    private $snippet1;

    /**
     * @var string
     */
    private $snippet1OriginalPath;

    public function setUp()
    {
        $this->initPhpcr();
        $this->contentMapper = $this->getContainer()->get('sulu.content.mapper');
        $this->session = $this->getContainer()->get('doctrine_phpcr')->getConnection();
        $this->loadFixtures();
    }

    public function loadFixtures()
    {
        $req = ContentMapperRequest::create()
            ->setType(Structure::TYPE_SNIPPET)
            ->setTemplateKey('animal')
            ->setLocale('de')
            ->setUserId(1)
            ->setData(array(
                'title' => 'ElePHPant',
            ))
            ->setState(StructureInterface::STATE_PUBLISHED);

        $this->snippet1 = $this->contentMapper->saveRequest($req);
        $this->snippet1OriginalPath = $this->session->getNodeByIdentifier($this->snippet1->getUuid())->getPath();
    }

    public function testChangeSnippetTemplate()
    {
        $req = ContentMapperRequest::create()
            ->setUuid($this->snippet1->getUuid())
            ->setType(Structure::TYPE_SNIPPET)
            ->setTemplateKey('hotel')
            ->setLocale('de')
            ->setState(StructureInterface::STATE_PUBLISHED)
            ->setUserId(1)
            ->setData(array(
                'title' => 'ElePHPant',
            ));
        $this->contentMapper->saveRequest($req);

        try {
            $this->session->getNode($this->snippet1OriginalPath);
            $this->assertTrue(false);
        } catch (\PHPCR\PathNotFoundException $e) {
            $this->assertTrue(true);
        }

        $node = $this->session->getNode('/cmf/snippets/hotel/elephpant');
        $node->getPropertyValue('template');
    }

    public function testRenameSnippet()
    {
        $req = ContentMapperRequest::create()
            ->setUuid($this->snippet1->getUuid())
            ->setType(Structure::TYPE_SNIPPET)
            ->setTemplateKey('animal')
            ->setLocale('de')
            ->setState(StructureInterface::STATE_PUBLISHED)
            ->setUserId(1)
            ->setData(array(
                'title' => 'ElePHPant FOOBAR',
            ));
        $this->contentMapper->saveRequest($req);
        $node = $this->session->getNode('/cmf/snippets/animal/elephpant');
        $node->getPropertyValue('template');
    }

    public function testRemoveSnippet()
    {
        $this->contentMapper->delete($this->snippet1->getUuid(), 'sulu_io');

        try {
            $this->session->getNode($this->snippet1OriginalPath);
            $this->assertTrue(false, 'Snippet was found FAIL');
        } catch (\PHPCR\PathNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    public function provideRemoveSnippetsWithReferences()
    {
        return array(
            array('sulu:page', 'cannot be removed'),
            array('sulu:content', 'cannot be removed'),
            array('sulu:path'),
        );
    }

    /**
     * @dataProvider provideRemoveSnippetsWithReferences
     */
    public function testRemoveSnippetWithReferences($referrerType, $exceptionMessage = null)
    {
        if (null !== $exceptionMessage) {
            $this->setExpectedException('PHPCR\ReferentialIntegrityException', $exceptionMessage);
        }

        $node = $this->session->getNode('/cmf')->addNode('test');
        $node->addMixin($referrerType);

        $node->setProperty('sulu:content', $this->snippet1->getUuid(), PropertyType::REFERENCE);
        $this->session->save();

        $this->contentMapper->delete($this->snippet1->getUuid(), 'sulu_io');

        try {
            $this->session->getNode($this->snippet1OriginalPath);
            $this->assertTrue(false, 'Snippet was found FAIL');
        } catch (\PHPCR\PathNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    public function testLoad()
    {
        $node = $this->session->getNode($this->snippet1OriginalPath);
        $snippet = $this->contentMapper->loadByNode(
            $node,
            'de',
            null,
            false,
            true
        );

        $templateKey = $snippet->getKey();
        $this->assertEquals('animal', $templateKey);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot change the structure type of
     */
    public function testUpdatePageWrongType()
    {
        $req = ContentMapperRequest::create()
            ->setUuid($this->snippet1->getUuid())
            ->setType(Structure::TYPE_PAGE)
            ->setWebspaceKey('sulu_io')
            ->setTemplateKey('test_page')
            ->setLocale('de')
            ->setState(StructureInterface::STATE_PUBLISHED)
            ->setUserId(1)
            ->setData(array('title' => 'Foo'));

        $this->contentMapper->saveRequest($req);
    }
}
