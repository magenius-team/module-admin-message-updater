<?php
/*
 * Copyright © Denis Kopylov (dba. Magenius.Team). All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Magenius\AdminMessageUpdater\Plugin\App\Action;

use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\Manager;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Serialize\SerializerInterface;

class AddMessagesToJsonResponse
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Manager
     */
    private $messageManager;

    /**
     * AddMessagesToJson constructor.
     * @param SerializerInterface $serializer
     * @param Manager $messageManager
     */
    public function __construct(
        SerializerInterface $serializer,
        Manager $messageManager
    ) {
        $this->serializer = $serializer;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Action $subject
     * @param ResponseInterface|ResultInterface $result
     * @param RequestInterface $request
     * @return ResultInterface|ResponseInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(
        Action $subject,
        $result,
        RequestInterface $request
    ) {
        if ($result instanceof Json && $request->isAjax()) {
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

            $reflectionResult = new \ReflectionClass($result);
            $jsonProperty = $reflectionResult->getProperty('json');
            $jsonProperty->setAccessible(true);
            $resultData = $jsonProperty->getValue($result) ?? [];
            $resultData = $this->serializer->unserialize($resultData);
            $resultData['controller_messages'] = $messages;
            $result->setData($resultData);
        }
        return $result;
    }
}
