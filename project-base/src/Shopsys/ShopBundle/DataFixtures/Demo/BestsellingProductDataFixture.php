<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade
     */
    protected $manualBestsellingProductFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade $manualBestsellingProductFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        ManualBestsellingProductFacade $manualBestsellingProductFacade,
        Domain $domain
    ) {
        $this->manualBestsellingProductFacade = $manualBestsellingProductFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();
            if ($domainId !== 2) {
                $this->manualBestsellingProductFacade->edit(
                    $this->getReference(CategoryDataFixture::CATEGORY_PHOTO),
                    $domainId,
                    [
                        0 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '7'),
                        2 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '8'),
                        8 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '5'),
                    ]
                );
            } else {
                $this->manualBestsellingProductFacade->edit(
                    $this->getReference(CategoryDataFixture::CATEGORY_PHOTO),
                    $domainId,
                    [$this->getReference(ProductDataFixture::PRODUCT_PREFIX . '7')]
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            ProductDataFixture::class,
            CategoryDataFixture::class,
        ];
    }
}
