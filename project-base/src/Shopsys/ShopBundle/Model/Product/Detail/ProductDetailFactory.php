<?php

namespace Shopsys\ShopBundle\Model\Product\Detail;

use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class ProductDetailFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculationForUser;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository
     */
    private $parameterRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        ProductRepository $productRepository,
        ParameterRepository $parameterRepository,
        ImageFacade $imageFacade,
        Localization $localization
    ) {
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->productRepository = $productRepository;
        $this->parameterRepository = $parameterRepository;
        $this->imageFacade = $imageFacade;
        $this->localization = $localization;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Detail\ProductDetail
     */
    public function getDetailForProduct(Product $product)
    {
        return new ProductDetail($product, $this);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @return \Shopsys\ShopBundle\Model\Product\Detail\ProductDetail[]
     */
    public function getDetailsForProducts(array $products)
    {
        $details = [];

        foreach ($products as $product) {
            $details[] = $this->getDetailForProduct($product);
        }

        return $details;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice|null
     */
    public function getSellingPrice(Product $product)
    {
        try {
            $productPrice = $this->productPriceCalculationForUser->calculatePriceForCurrentUser($product);
        } catch (\Shopsys\ShopBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
            $productPrice = null;
        }
        return $productPrice;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
     */
    public function getParameters(Product $product)
    {
        $locale = $this->localization->getLocale();

        $productParameterValues = $this->parameterRepository->getProductParameterValuesByProductSortedByName($product, $locale);
        foreach ($productParameterValues as $index => $productParameterValue) {
            $parameter = $productParameterValue->getParameter();
            if ($parameter->getName() === null
                || $productParameterValue->getValue()->getLocale() !== $locale
            ) {
                unset($productParameterValues[$index]);
            }
        }

        return $productParameterValues;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Component\Image\Image[]
     */
    public function getImagesIndexedById(Product $product)
    {
        return $this->imageFacade->getImagesByEntityIndexedById($product, null);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\ProductDomain[]
     */
    public function getProductDomainsIndexedByDomainId(Product $product)
    {
        return $this->productRepository->getProductDomainsByProductIndexedByDomainId($product);
    }
}