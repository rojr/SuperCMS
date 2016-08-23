<?php

namespace SuperCMS\UrlHandlers;

use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use SuperCMS\Layouts\AdminLayout;

class AdminClassMappedUrlHandler extends ClassMappedUrlHandler
{
    public function generateResponseForRequest($request = null)
    {
        LayoutModule::setLayoutClassName(AdminLayout::class);
        return parent::generateResponseForRequest($request);
    }
}