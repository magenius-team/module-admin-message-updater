<?php
/*
 * Copyright © Denis Kopylov (dba. Magenius.Team) https://github.com/magenius-team
 * See LICENSE distributed with the module for license details.
 */
declare(strict_types=1);

namespace Magenius\AdminMessageUpdater\Controller\Adminhtml\Messages;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Serialize\Serializer\Json;

class GetMessages extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = "Magenius_AdminMessageUpdater::admin_messages";
    /**
     * @var Json
     */
    protected $serializer;

    /**
     * GetMessages constructor.
     * @param Action\Context $context
     * @param Json $serializer
     */
    public function __construct(
        Action\Context $context,
        Json $serializer
    ) {
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result = $this->getMessages();

        $resultJson->setData($this->serializer->serialize($result));
        return $resultJson;
    }

    /**
     * Retrieve messages by types
     *
     * @return array
     */
    private function getMessages(): array
    {
        $messages = [
            MessageInterface::TYPE_ERROR => [],
            MessageInterface::TYPE_SUCCESS => []
        ];

        $items = $this->messageManager->getMessages(true)->getItems();
        foreach ($items as $message) {
            if (isset($messages[$message->getType()])) {
                $messages[$message->getType()][] = $message->getText();
            }
        }

        return $messages;
    }
}
