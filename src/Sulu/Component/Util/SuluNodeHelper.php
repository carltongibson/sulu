<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Util;

use PHPCR\NodeInterface;
use PHPCR\Util\PathHelper;
use Sulu\Component\Content\Structure;

/**
 * Utility class for extracting Sulu-centric properties from nodes.
 * Note this should be removed when we have domain objects.
 */
class SuluNodeHelper
{
    /**
     * @var string
     */
    private $languageNamespace;

    /**
     * @param string $languageNamespace
     * @param array $paths Path segments from configuration
     */
    public function __construct($languageNamespace, $paths)
    {
        $this->languageNamespace = $languageNamespace;
        $this->paths = array_merge(array(
            'base' => null,
            'content' => null,
            'route' => null,
            'temp' => null,
            'snippet' => null
        ), $paths);
    }

    /**
     * Return the languages that are currently registered on the
     * given PHPCR node.
     *
     * @param NodeInterface $node
     * @return array
     */
    public function getLanguagesForNode(NodeInterface $node)
    {
        $languages = array();
        foreach ($node->getProperties() as $property) {
            preg_match('/^' . $this->languageNamespace . ':(.*?)-title/', $property->getName(), $matches);

            if ($matches) {
                $languages[$matches[1]] = $matches[1];
            }
        }

        return array_values($languages);
    }

    /**
     * Return the structure type for the given node
     *
     * @param NodeInterface $node
     *
     * @return string
     */
    public function getStructureTypeForNode(NodeInterface $node)
    {
        $mixinTypes = $node->getPropertyValueWithDefault('jcr:mixinTypes', array());

        if (in_array('sulu:' . Structure::TYPE_PAGE, $mixinTypes)) {
            return Structure::TYPE_PAGE;
        }

        if (in_array('sulu:' . Structure::TYPE_SNIPPET, $mixinTypes)) {
            return Structure::TYPE_SNIPPET;
        }

        return null;
    }

    /**
     * Return true if the given node has the given
     * nodeType property (or properties).
     *
     * The sulu node type is the localname of node types
     * with the sulu namespace.
     *
     * Example:
     *   sulu:snippet is the PHPCR node type
     *   snippet is the Sulu node type
     *
     * @param NodeInterface $nodeTypes
     * @param string|array $nodeTypes One or more node sulu types
     */
    public function hasSuluNodeType($node, $suluNodeTypes)
    {
        foreach ((array) $suluNodeTypes as $suluNodeType) {
            if (in_array($suluNodeType, $node->getPropertyValueWithDefault('jcr:mixinTypes', array()))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extracts webspace key from given path
     *
     * TODO: We should inject the base path here
     *
     * @param string $path path of node
     * @return string
     */
    public function extractWebspaceFromPath($path)
    {
        $match = preg_match('/^\/' . $this->getPath('base') . '\/(\w*)\/.*$/', $path, $matches);

        if ($match) {
            return $matches[1];
        } else {
            return null;
        }
    }

    /**
     * Extract the snippet path from the given path
     *
     * @param string $path
     *
     * @return string
     */
    public function extractSnippetTypeFromPath($path)
    {
        if (substr($path, 0, 1) !== '/') {
            throw new \InvalidArgumentException(
                sprintf(
                    'Path must be absolute, got "%s"',
                    $path
                )
            );
        }

        $snippetsPath = '/' . $this->getPath('base') . '/' . $this->getPath('snippet') . '/';
        $newPath = PathHelper::getParentPath($path);
        $newPath = substr($newPath, strlen($snippetsPath));

        if (false === $newPath) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot extract snippet template type from path "%s"',
                    $path
                )
            );
        }

        return $newPath;
    }

    /**
     * Return the configured named path segment
     *
     * @param string $name Name of path segment
     * @return string The path segment
     */
    private function getPath($name)
    {
        if (!isset($this->paths[$name])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unknown path segment name "%s", known paths are "%s"',
                    $name,
                    implode('", "', array_keys($this->paths))
                )
            );
        }

        $name = $this->paths[$name];

        return $name;
    }
}
