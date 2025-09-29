<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\PageTitle\DocsPageTitleProvider;
use FluidPrimitives\Docs\Services\NavigationBuilder;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Symfony\Component\Yaml\Yaml;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Query;
use League\CommonMark\Renderer\HtmlRenderer;
use Phiki\Adapters\CommonMark\PhikiExtension;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use Phiki\Transformers\Decorations\PreDecoration;
use TYPO3\CMS\Core\Core\Environment as Typo3Environment;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\Core\Component\ComponentDefinitionProviderInterface;
use TYPO3Fluid\Fluid\Core\Component\ComponentTemplateResolverInterface;

class DocsController extends ActionController
{
    private ?MarkdownConverter $converter = null;

    public function __construct(
        private readonly ViewFactoryInterface $viewFactory,
        private readonly NavigationBuilder $navigationBuilder,
        private readonly RenderingContextFactory $renderingContextFactory,
        private readonly DocsPageTitleProvider $pageTitleProvider
    ) {}

    public function showAction(string $path = ''): ResponseInterface
    {
        if ($path === '') {
            $this->view->assign('layout', 'home');
            $this->pageTitleProvider->setTitle('Fluid Primitives – The headless component library for TYPO3 Fluid');
            return $this->htmlResponse();
        }

        if ($path === 'playground' && Typo3Environment::getContext()->isDevelopment()) {
            $this->view->assign('layout', 'playground');
            $this->pageTitleProvider->setTitle('Playground – Fluid Primitives');
            return $this->htmlResponse();
        }

        $baseDir = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/');
        $filePath = $baseDir . rtrim($path, '/') . '.md';

        if (!is_file($filePath)) {
            $redirects = Yaml::parseFile($baseDir . 'redirects.yaml') ?? [];
            $target = $redirects[rtrim($path, '/')] ??= null;
            if ($target) {
                return $this->redirectToUri($target, 302);
            }

            $this->view->assign('layout', '404');
            $this->pageTitleProvider->setTitle('Not Found – Fluid Primitives');
            return $this->htmlResponse()->withStatus(404);
        }

        [$meta, $markdown] = $this->parseMarkdownFile($filePath);

        $processedMarkdown = $this->processFluidTemplates($markdown);
        $converter = $this->getMarkdownConverter();
        $converted = $converter->convert($processedMarkdown);

        $document = $converted->getDocument();

        $toc = (new Query())
            ->where(Query::type(TableOfContents::class))
            ->findOne($document);
        if ($toc) {
            $toc->detach();
        }

        $renderer = new HtmlRenderer($converter->getEnvironment());
        $content = $renderer->renderDocument($document);

        $content = $this->wrapCodeBlocks($content->getContent());

        if ($toc) {
            $toc = $renderer->renderNodes([$toc]);
            $toc = str_replace('<ul class="table-of-contents">', '<ul class="table-of-contents"><li><a href="#">(Top)</a></li>', $toc);
        }

        $this->view->assignMultiple([
            'content' => (string)$content,
            'toc' => $toc,
            'nav' => $this->navigationBuilder->buildNavigation($baseDir, $baseDir . 'nav.yaml'),
            'meta' => $meta,
            'path' => '/' . $path,
        ]);

        $this->pageTitleProvider->setTitle(($meta['title'] ?? 'Documentation') . ' – Fluid Primitives');

        return $this->htmlResponse();
    }

    private function getMarkdownConverter(): MarkdownConverter
    {
        if ($this->converter === null) {
            $environment = new Environment([
                'heading_permalink' => [
                    'min_heading_level' => 2,
                    'max_heading_level' => 3,
                    'apply_id_to_heading' => true,
                    'title' => '',
                    'symbol' => '',
                    'insert' => 'after'
                ],
                'external_link' => [
                    'internal_hosts' => $_SERVER['HTTP_HOST'],
                    'open_in_new_window' => true,
                ],
                'table_of_contents' => [
                    'html_class' => 'table-of-contents',
                    'position' => 'top',
                    'style' => 'bullet',
                    'min_heading_level' => 2,
                    'max_heading_level' => 3,
                    'normalize' => 'relative',
                    'placeholder' => null,
                ],
            ]);

            $environment
                ->addExtension(new CommonMarkCoreExtension())
                ->addExtension(new PhikiExtension(Theme::MinLight))
                ->addExtension(new HeadingPermalinkExtension())
                ->addExtension(new TableOfContentsExtension())
                ->addExtension(new ExternalLinkExtension())
                ->addExtension(new TableExtension());

            $this->converter = new MarkdownConverter($environment);
        }

        return $this->converter;
    }

    private function processFluidTemplates(string $markdown): string
    {
        return preg_replace_callback(
            '/{% component:\s*"([^"]+)"(?:,\s*arguments:\s*({.*?}))?\s*%}/',
            function ($matches) {
                $fullViewHelperName = $matches[1];
                $arguments = isset($matches[2]) ? json_decode($matches[2], true) ?? [] : [];

                try {
                    $renderingContext = $this->renderingContextFactory->create(request: $this->request);

                    [$namespace, $viewHelperName] = explode(':', $fullViewHelperName);
                    $viewHelperResolverDelegate = $renderingContext->getViewHelperResolver()->getResponsibleDelegate(
                        $namespace,
                        $viewHelperName
                    );

                    if (!$viewHelperResolverDelegate instanceof ComponentDefinitionProviderInterface || !$viewHelperResolverDelegate instanceof ComponentTemplateResolverInterface) {
                        return '<div class="fluid-template-error">Error: Unknown component "' . htmlspecialchars($viewHelperName) . '"</div>';
                    }

                    $isCodeExample = str_contains($fullViewHelperName, 'examples');

                    $componentRenderer = $viewHelperResolverDelegate->getComponentRenderer();
                    $html = $componentRenderer->renderComponent($viewHelperName, [...$arguments, 'class' => 'not-prose'], [], $renderingContext);

                    if ($isCodeExample) {
                        $templateName = $viewHelperResolverDelegate->resolveTemplateName($viewHelperName);
                        $templateString = $viewHelperResolverDelegate->getTemplatePaths()->getTemplateSource('Default', $templateName);

                        $highlightedTemplate = (new Phiki)
                            ->codeToHtml($templateString, Grammar::Html, Theme::GithubLight)
                            ->decoration(PreDecoration::make()->class('not-code-block'))
                            ->toString();

                        $html = $componentRenderer->renderComponent('ComponentExample', [
                            'html' => $html,
                            'templateHighlighted' => $highlightedTemplate,
                            'templateRaw' => $templateString,
                        ], [], $renderingContext);
                    } else {
                        $html = '<div class="prose-component">' . $html . '</div>';
                    }

                    $html = $this->cleanHtmlForMarkdown($html);

                    return $html;
                } catch (\Exception $e) {
                    return '<div class="fluid-template-error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            },
            $markdown
        );
    }

    private function parseMarkdownFile(string $filePath): array
    {
        $content = file_get_contents($filePath);

        if (preg_match('/^---\n(.*?)\n---\n/s', $content, $matches)) {
            $yaml = $matches[1];
            $meta = Yaml::parse($yaml) ?? [];
            $markdown = substr($content, strlen($matches[0]));
        } else {
            $meta = [];
            $markdown = $content;
        }

        if (empty($meta['title']) && preg_match('/^#\s+(.+)$/m', $markdown, $h1Match)) {
            $meta['title'] = trim($h1Match[1]);
        }

        return [$meta, $markdown];
    }

    private function wrapCodeBlocks(string $html): string
    {
        // Match <pre> tags that do NOT have class="not-code-block"
        $pattern = '/(<pre\b(?![^>]*\bclass\s*=\s*["\'][^"\']*\bnot-code-block\b[^"\']*["\']).*?<\/pre>)/is';
        $replacement = '<div class="code-block"><div>$1</div></div>';

        return preg_replace($pattern, $replacement, $html);
    }

    private function cleanHtmlForMarkdown(string $html): string
    {
        // Extract <pre> blocks so we don't accidentally clean them up
        $preBlocks = [];
        $html = preg_replace_callback(
            '/<pre\b[^>]*>[\s\S]*?<\/pre>/i',
            function ($matches) use (&$preBlocks) {
                $key = '###PRE_BLOCK_' . count($preBlocks) . '###';
                $preBlocks[$key] = $matches[0];
                return $key;
            },
            $html
        );

        // Remove HTML comments
        $html = preg_replace('/<!--[\s\S]*?-->/', '', $html);
        // Collapse whitespace between tags
        $html = preg_replace('/>\s+</', '><', $html);
        // Collapse excessive whitespace inside tags/attributes
        $html = preg_replace('/\s{2,}/', ' ', $html);
        // Trim leading/trailing whitespace
        $html = trim($html);

        // Restore <pre> blocks
        $html = strtr($html, $preBlocks);

        return $html;
    }
}
