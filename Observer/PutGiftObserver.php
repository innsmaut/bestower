<?php

namespace Local\Bestower\Observer;

class PutGiftObserver implements \Magento\Framework\Event\ObserverInterface
{
    private $session;
    private $itemFactory;
    private $productRepository;
    private $giftId;
    private $giftSum;
    private $giftState;
    private $giftQuoteId;

    public function __construct(\Magento\Checkout\Model\Session $session,
                                \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                                \Local\Bestower\Helper\Data $config)
    {
        $this->session = $session;
        $this->itemFactory = $itemFactory;
        $this->productRepository = $productRepository;
        $this->giftId = $config->getGiftId();
        $this->giftSum = $config->getGiftSum();
        $this->giftState = false;
        $this->giftQuoteId = null;
    }

    private function searchForGift(){
        foreach ($this->session->getQuote()->getAllVisibleItems() as $item){
            if (($item->getProduct()->getId() == $this->giftId)
                && ($item->getPrice() == 0)) {
                $this->giftQuoteId = $item->getId();
                return true;
            }
        }
        return false;
    }

    private function compareSum(){
        return $this->session->getQuote()->getSubtotal() >= $this->giftSum;
    }

    private function prepareGift(){
        $item = $this->itemFactory->create();
        $giftProduct = $this->productRepository->getById($this->giftId);
        $item->setProduct($giftProduct);
        $item->setQty(1);
        $item->setOriginalCustomPrice(0);
        return $item;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->giftState = $this->searchForGift();

        if (!$this->giftState){
            if ($this->compareSum()) {
            $this->session->getQuote()->addItem($this->prepareGift());
            $this->session->getQuote()->save();
            }
        } else {
            if (!$this->compareSum()) {
                $this->session->getQuote()->removeItem($this->giftQuoteId);
                $this->session->getQuote()->save();
            }
        }
    }
}