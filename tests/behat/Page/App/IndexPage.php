<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Behat\Page\App;

use Behat\Mink\Element\NodeElement;
use Netgen\BlockManager\Behat\Exception\PageException;
use Netgen\BlockManager\Behat\Page\SymfonyPage;

final class IndexPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return 'ngbm_app';
    }

    public function verifyLayout(string $layoutName): void
    {
        $this->waitForElement(10, 'layout_name', ['%layout-name%' => $layoutName]);

        if ($this->hasElement('layout_name', ['%layout-name%' => $layoutName])) {
            return;
        }

        throw new PageException(sprintf('Expected to have an element with "%s" layout name but none found', $layoutName));
    }

    public function verifyCreateForm(bool $shared = false): void
    {
        $this->waitForElement(10, 'create_layout_form');

        if (!$this->hasElement('create_layout_form')) {
            throw new PageException('Expected to have a form for creating a layout but none found.');
        }

        if (!$this->getDocument()->hasField('create[shared]')) {
            throw new PageException('Expected to have a field named "create[shared]" but none found.');
        }

        $sharedLayoutField = $this->getDocument()->findField('create[shared]');
        if (!$sharedLayoutField instanceof NodeElement) {
            throw new PageException('Expected to have a field named "create[shared]", but found none.');
        }

        $sharedLayoutValue = $sharedLayoutField->getValue();

        if ($shared && $sharedLayoutValue !== '1') {
            throw new PageException(sprintf('Expected to have a field named "create[shared]" with value "1", but found value "%s".', var_export($sharedLayoutValue, true)));
        }

        if (!$shared && $sharedLayoutValue === '1') {
            throw new PageException('Expected to have a field named "create[shared]" with value different from "1", but found value "1".');
        }
    }

    protected function getDefinedElements(): array
    {
        return [
            'create_layout_form' => '.modal-dialog form[name="create"]',
            'layout_name' => 'span.js-layout-name:contains("%layout-name%")',
        ];
    }
}
