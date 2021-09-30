<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Customize\Form\Extension\Front;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractTypeExtension;
use Eccube\Form\Type\Front\CustomerLoginType;

class CustomerLoginTypeExtension extends AbstractTypeExtension
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var AuthenticationUtils
     */
    protected $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils, EccubeConfig $eccubeConfig)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('login_email')
            ->add('customer_code', TextType::class, [
                'attr' => [
                    'max_length' => $this->eccubeConfig['eccube_stext_len'],
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'data' => $this->authenticationUtils->getLastUsername(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CustomerLoginType::class;
    }
}