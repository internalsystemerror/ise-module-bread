<?php

namespace Ise\Bread\Router\Http;

use Zend\Mvc\Router\Http\Literal;

class Bread extends Literal
{

    const IDENTIFIER     = 'id';
    
    const ACTION_INDEX   = 'browse';
    const ACTION_CREATE  = 'add';
    const ACTION_READ    = 'read';
    const ACTION_UPDATE  = 'edit';
    const ACTION_DELETE  = 'delete';
    const ACTION_ENABLE  = 'enable';
    const ACTION_DISABLE = 'disable';
    const ACTIONS        = [
        self::ACTION_READ,
        self::ACTION_CREATE,
        self::ACTION_UPDATE,
        self::ACTION_DELETE,
        self::ACTION_ENABLE,
        self::ACTION_DISABLE
    ];
    
    const FORM_CREATE = self::ACTION_CREATE;
    const FORM_UPDATE = self::ACTION_UPDATE;
    const FORM_DIALOG = 'dialog';
    const FORMS       = [
        self::FORM_CREATE,
        self::FORM_UPDATE,
        self::FORM_DIALOG
    ];
}
