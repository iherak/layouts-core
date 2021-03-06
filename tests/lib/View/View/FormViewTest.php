<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\View\View\FormView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Forms;

final class FormViewTest extends TestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    private $form;

    /**
     * @var \Netgen\BlockManager\View\View\FormViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->getFormFactory();

        $this->form = $formFactory->create(FormType::class);

        $this->view = new FormView($this->form);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('form', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\FormView::__construct
     * @covers \Netgen\BlockManager\View\View\FormView::getForm
     * @covers \Netgen\BlockManager\View\View\FormView::getFormType
     * @covers \Netgen\BlockManager\View\View\FormView::getFormView
     */
    public function testGetForm(): void
    {
        self::assertSame($this->form, $this->view->getForm());
        self::assertSame(FormType::class, $this->view->getFormType());

        self::assertSame($this->view->getFormView(), $this->view->getParameter('form'));
        self::assertSame('value', $this->view->getParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\FormView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('form', $this->view::getIdentifier());
    }
}
