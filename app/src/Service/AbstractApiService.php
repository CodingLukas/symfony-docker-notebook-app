<?php

namespace App\Service;

use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validation;

abstract class AbstractApiService
{
    protected $formFactory;
    protected $serializer;

    public function __construct()
    {
        $validator = Validation::createValidator();
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();

    }

    protected function buildForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->createNamed('', $type, $data, $options);
    }

    protected function getExtensions()
    {
        return new ValidatorExtension(Validation::createValidator());
    }

    protected function prepareJSON($items, $attributes){
        return  $this->serializer->normalize($items, 'json', [AbstractNormalizer::ATTRIBUTES => $attributes]);
    }
}
