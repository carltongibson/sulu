<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ContactBundle\Api;

use Sulu\Bundle\ContactBundle\Entity\Account as AccountEntity;
use Doctrine\Entity;
use Sulu\Bundle\ContactBundle\Entity\AccountAddress as AccountAddressEntity;
use Sulu\Bundle\ContactBundle\Entity\AccountCategory as AccountCategoryEntity;
use Sulu\Bundle\ContactBundle\Entity\AccountContact as AccountContactEntity;
use Sulu\Bundle\ContactBundle\Entity\Activity as ActivityEntity;
use Sulu\Bundle\ContactBundle\Entity\ActivityPriority as ActivityPriorityEntity;
use Sulu\Bundle\ContactBundle\Entity\ActivityStatus as ActivityStatusEntity;
use Sulu\Bundle\ContactBundle\Entity\ActivityType as ActivityTypeEntity;
use Sulu\Bundle\ContactBundle\Entity\AddressType as AddressTypeEntity;
use Sulu\Bundle\ContactBundle\Entity\BankAccount as BankAccountEntity;
use Sulu\Bundle\ContactBundle\Entity\Contact as ContactEntity;
use Sulu\Bundle\ContactBundle\Entity\ContactAddress as ContactAddressEntity;
use Sulu\Bundle\ContactBundle\Entity\Country;
use Sulu\Bundle\ContactBundle\Entity\Email as EmailEntity;
use Sulu\Bundle\ContactBundle\Entity\Fax as FaxEntity;
use Sulu\Bundle\ContactBundle\Entity\Note as NoteEntity;
use Sulu\Bundle\ContactBundle\Entity\Phone as PhoneEntity;
use Sulu\Bundle\ContactBundle\Entity\Address as AddressEntity;
use Sulu\Bundle\ContactBundle\Entity\TermsOfDelivery as TermsOfDeliveryEntity;
use Sulu\Bundle\ContactBundle\Entity\TermsOfPayment as TermsOfPaymentEntity;
use Sulu\Bundle\ContactBundle\Entity\Url as UrlEntity;
use Sulu\Bundle\MediaBundle\Api\Media;
use Sulu\Bundle\MediaBundle\Entity\Media as MediaEntity;
use Sulu\Component\Rest\ApiWrapper;
use Sulu\Component\Security\UserInterface;
use Hateoas\Configuration\Annotation\Relation;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Groups;

/**
 * The ContactLocale class which will be exported to the API
 *
 * @package Sulu\Bundle\ContactBundle\Api
 * @ExclusionPolicy("all")
 */
class ContactLocale extends ApiWrapper
{
    /**
     * @param ContactLocale $contactLocale
     */
    public function __construct(ContactLocale $contactLocale)
    {
        $this->entity = $contactLocale;
    }

    /**
     * Returns the id of the product
     *
     * @return int
     * @VirtualProperty
     * @SerializedName("id")
     */
    public function getId()
    {
        return $this->entity->getId();
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return ContactLocale
     */
    public function setLocale($locale)
    {
        $this->entity->setLocale($locale);

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     * @VirtualProperty
     * @SerializedName("locale")
     */
    public function getLocale()
    {
        return $this->entity->getLocale();
    }
}
