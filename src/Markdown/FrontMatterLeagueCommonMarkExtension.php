<?php

/*
 * This is part of the webuni/front-matter package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\FrontMatter\Markdown;

use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Input\MarkdownInput;
use Webuni\FrontMatter\FrontMatterInterface;

class FrontMatterLeagueCommonMarkExtension implements ExtensionInterface
{
    /** @var FrontMatterInterface */
    private $frontMatter;

    public function __construct(FrontMatterInterface $frontMatter)
    {
        $this->frontMatter = $frontMatter;
    }

    public function register(ConfigurableEnvironmentInterface $environment): void
    {
        $environment->addEventListener(DocumentPreParsedEvent::class, [$this, 'parse']);
    }

    public function parse(DocumentPreParsedEvent $event): void
    {
        $content = $event->getMarkdown()->getContent();
        $document = $this->frontMatter->parse($content);
        $data = $event->getDocument()->data;

        $event->getDocument()->data = array_merge($document->getData(), $data);
        $event->replaceMarkdown(new MarkdownInput($document->getContent()));
    }
}
