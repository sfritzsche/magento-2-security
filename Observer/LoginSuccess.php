<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Security
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Security\Observer;

use Magento\Backend\Model\Session;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Mageplaza\Security\Helper\Data;
use Mageplaza\Security\Model\Config\Source\LoginLog\Status;
use Mageplaza\Security\Model\LoginLogFactory;

/**
 * Class LoginSuccess
 * @package Mageplaza\Security\Observer
 */
class LoginSuccess implements ObserverInterface
{
    /**
     * @var RemoteAddress
     */
    protected $_address;

    /**
     * @var Session
     */
    protected $_backendSession;

    /**
     * @var LoginLogFactory
     */
    protected $_loginLogFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * LoginSuccess constructor.
     * @param RemoteAddress $address
     * @param Session $session
     * @param LoginLogFactory $loginLogFactory
     * @param Data $helperData
     */
    public function __construct(
        RemoteAddress $address,
        Session $session,
        LoginLogFactory $loginLogFactory,
        Data $helperData
    )
    {
        $this->_address         = $address;
        $this->_backendSession  = $session;
        $this->_loginLogFactory = $loginLogFactory;
        $this->_helperData      = $helperData;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helperData->isEnabled()) {
            $loginLog = [
                'time'          => time(),
                'user_name'     => $observer->getUser()->getUserName(),
                'ip'            => $this->_address->getRemoteAddress(),
                'browser_agent' => $this->_backendSession->getBrowserAgent(),
                'url'           => $this->_backendSession->getUrl(),
                'referer'       => $this->_backendSession->getRefererUrl(),
                'status'        => Status::STATUS_SUCCESS
            ];
            $this->_loginLogFactory->create()->addData($loginLog)->save();
        }
    }
}