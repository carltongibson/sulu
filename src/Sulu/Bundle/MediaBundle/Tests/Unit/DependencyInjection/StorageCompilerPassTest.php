<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\MediaBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sulu\Bundle\MediaBundle\DependencyInjection\StorageCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Test the image command compiler pass
 */
class StorageCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new StorageCompilerPass());
    }

    /**
     * @test
     */
    public function if_compiler_pass_collects_services_by_adding_method_calls_these_will_exist()
    {
        $commandManager = new Definition();
        $this->setDefinition('sulu_media.storage_manager', $commandManager);

        $this->setParameter('sulu_media.storage.adapters', array(
            'test' => array(
                'type' => 'local',
                'segments' => '10',
                'uploadPath' => '%kernel.root_dir%/../uploads/media',
            )
        ));

        $command = new Definition();
        $command->addTag('sulu_media.storage_adapter', array('alias' => 'local'));
        $this->setDefinition('sulu_media.storage.adapter.local', $command);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sulu_media.storage_manager',
            'add',
            array(
                new Reference('sulu_media.test_storage'),
                'test'
            )
        );
    }
}
