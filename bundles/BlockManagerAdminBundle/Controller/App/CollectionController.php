<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\QueryDraft;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     */
    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Displays and processes query draft edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If query does not support the specified form
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function queryEditForm(QueryDraft $query, $formName, Request $request)
    {
        $queryType = $this->getQueryType($query->getType());

        if (!$queryType->getConfig()->hasForm($formName)) {
            throw new InvalidArgumentException('form', 'Query does not support specified form.');
        }

        $queryForm = $queryType->getConfig()->getForm($formName);

        $updateStruct = $this->collectionService->newQueryUpdateStruct($query);

        $form = $this->createForm(
            $queryForm->getType(),
            $updateStruct,
            array(
                'queryType' => $queryType,
                'action' => $this->generateUrl(
                    'netgen_block_manager_app_collection_query_form_edit',
                    array(
                        'queryId' => $query->getId(),
                        'formName' => $formName,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->buildView($form);
        }

        if ($form->isValid()) {
            $this->collectionService->updateQuery($query, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_VIEW,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}