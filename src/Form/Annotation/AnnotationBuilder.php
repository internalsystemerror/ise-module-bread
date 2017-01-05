<?php

namespace Ise\Bread\Form\Annotation;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;

class AnnotationBuilder extends DoctrineAnnotationBuilder
{
    
    /**
     * {@inheritDoc}
     */
    public function getFormSpecification($entity)
    {
        $formSpec = parent::getFormSpecification($entity);
        $formSpec['options']['prefer_form_input_filter'] = false;
        return $formSpec;
    }
}
