<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Form\Annotation;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;

class AnnotationBuilder extends DoctrineAnnotationBuilder
{

    /**
     * @inheritdoc
     */
    public function getFormSpecification($entity): array
    {
        $formSpec                                        = (array)parent::getFormSpecification($entity);
        $formSpec['options']['prefer_form_input_filter'] = false;
        return $formSpec;
    }
}
