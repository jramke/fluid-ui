<?php

declare(strict_types=1);

namespace FluidUI\Docs\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

class DocsPageTitleProvider extends AbstractPageTitleProvider
{
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
