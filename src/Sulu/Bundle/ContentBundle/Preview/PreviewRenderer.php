<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ContentBundle\Preview;

use Liip\ThemeBundle\ActiveTheme;
use Sulu\Component\Content\StructureInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class PreviewRenderer
{
    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    /**
     * @var ActiveTheme
     */
    private $activeTheme;

    /**
     * @var ControllerResolverInterface
     */
    private $controllerResolver;

    public function __construct(ActiveTheme $activeTheme, ControllerResolverInterface $controllerResolver, WebspaceManagerInterface $webspaceManager)
    {
        $this->activeTheme = $activeTheme;
        $this->controllerResolver = $controllerResolver;
        $this->webspaceManager = $webspaceManager;
    }

    /**
     * renders content with the real website controller
     * @param StructureInterface $content
     * @param bool $partial
     * @return string
     */
    public function render(StructureInterface $content, $partial = false)
    {
        // set active theme
        $webspace = $this->webspaceManager->findWebspaceByKey($content->getWebspaceKey());
        $this->activeTheme->setName($webspace->getTheme()->getKey());

        // get controller and invoke action
        $request = new Request();
        $request->attributes->set('_controller', $content->getController());
        $controller = $this->controllerResolver->getController($request);

        /** @var Response $response */
        $response = $controller[0]->{$controller[1]}($content, true, $partial);

        return $response->getContent();
    }
}
