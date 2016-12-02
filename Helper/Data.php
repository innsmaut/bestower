<?php

namespace Local\Bestower\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SUM = 'bestower/config/sum';
    const ID = 'bestower/config/gift_id';
    
    public function __construct(\Magento\Framework\App\Helper\Context $context) {
        parent::__construct($context);
    }

    public function getGiftSum(){
        return $this->scopeConfig->getValue(
            self::SUM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGiftId(){
        return $this->scopeConfig->getValue(
            self::ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}