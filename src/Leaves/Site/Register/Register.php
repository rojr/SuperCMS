<?php

namespace SuperCMS\Leaves\Site\Register;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Leaf\Crud\Leaves\CrudLeaf;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;
use SuperCMS\Models\User\SuperCMSUser;

class Register extends CrudLeaf
{
    /** @var ReM */
    protected $model;

    protected function getViewClass()
    {
        return RegisterView::class;
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();

        $this->model->restModel = new SuperCMSUser();
    }

    protected function createModel()
    {
        return new RegisterModel();
    }

    protected function saveRestModel()
    {
        /** @var SuperCMSUser $obj */
        $obj = $this->model->restModel;

        if ($obj->Password == $this->model->PasswordRepeat) {

            $obj->setNewPassword($this->model->PasswordRepeat);
            $obj->Username = $obj->Email;

            try {
                $obj = parent::saveRestModel();
            } catch (ModelConsistencyValidationException $ex) {

            }

            return $obj;
        }
    }

    protected function redirectAfterSave()
    {
        $request = Request::current();
        if ($request->get('rd')) {
            $url = base64_decode($request->get('rd'));
        } else {
            $url = '/';
        }
        throw new ForceResponseException(new RedirectResponse($url));
    }
}
