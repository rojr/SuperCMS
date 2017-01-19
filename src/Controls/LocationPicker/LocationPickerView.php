<?php

namespace SuperCMS\Controls\LocationPicker;

use Rhubarb\Leaf\Controls\Common\SelectionControls\DropDown\DropDown;
use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Leaf\Leaves\Controls\ControlView;
use Rhubarb\Leaf\Leaves\LeafDeploymentPackage;
use SuperCMS\Controls\HtmlButton\HtmlButton;
use SuperCMS\Models\User\Location;

class LocationPickerView extends ControlView
{
    protected $requiresContainerDiv = true;

    /** @var LocationPickerModel */
    protected $model;

    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            new TextBox('Recipient'),
            new TextBox('AddressLine1'),
            new TextBox('AddressLine2'),
            new TextBox('Town'),
            new TextBox('PostCode'),
            new TextBox('PhoneNumber'),
            $country = new DropDown('Country'),
            $save = new HtmlButton('Save', 'Save', function() {
                $this->model->saveEvent->raise();
            }),
            $cancel = new HtmlButton('Cancel', 'Cancel', function() {

            }, true)
        );

        $country->setSelectionItems(['Please Select']);

        foreach ($this->leaves as $leaf) {
            if ($leaf instanceof TextBox) {
                $leaf->addCssClassNames('form-control');
            }
        }

        $save->addCssClassNames('btn', 'button', 'button-checkout');
        $cancel->addCssClassNames('btn', 'button', 'button-checkout', 'button--secondary');
    }

    protected function printViewContent()
    {
        $this->printModal();
        $this->printAddressList();
    }

    protected function printAddressList()
    {
        print '<div class="row">';
        foreach($this->model->user->Locations as $location) {
            $this->printAddress($location);
        }
        print '</div>';
    }

    protected function printAddress(Location $location)
    {
        print <<<HTML
        <div class="col-md-4">
            <p>{$location->Recipient}</p>
            <p>{$location->AddressLine1}</p>
            <p>{$location->AddressLine2}</p>
            <p>{$location->Town}</p>
            <p>{$location->PostCode}</p>
            <p>{$location->Country}</p>
            <p>{$location->PhoneNumber}</p>
            <div class="row">
                <div class="c-md-6">
                    <a class="js-location-edit" href="" data-id="{$location->UniqueIdentifier}" data-toggle="modal" data-target=".modal-location-edit">Edit</a>
                </div>
                <div class="c-md-6">
                    <a class="js-location-delete" href="" data-id="{$location->UniqueIdentifier}">Delete</a>
                </div>
            </div>
        </div>
HTML;
    }

    protected function printModal()
    {
        ?>
        <button type="button" class="btn btn-primary js-add-location" data-toggle="modal" data-target=".modal-location-edit">Add a new Location</button>

        <div class="modal fade modal-location-edit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Location Management</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label>Recipient</label>
                                <?= $this->leaves['Recipient'] ?>
                            </div>
                            <div class="form-group">
                                <label>Address Line 1</label>
                                <?= $this->leaves['AddressLine1'] ?>
                            </div>
                            <div class="form-group">
                                <label>Address Line 2</label>
                                <?= $this->leaves['AddressLine2'] ?>
                            </div>
                            <div class="form-group">
                                <label>Town</label>
                                <?= $this->leaves['Town'] ?>
                            </div>
                            <div class="form-group">
                                <label>Post Code</label>
                                <?= $this->leaves['PostCode'] ?>
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <?= $this->leaves['Country'] ?>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <?= $this->leaves['PhoneNumber'] ?>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <?= $this->leaves['Cancel'] ?>
                        <?= $this->leaves['Save'] ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    protected function getViewBridgeName()
    {
        return 'LocationPickerViewBridge';
    }

    public function getDeploymentPackage()
    {
        return new LeafDeploymentPackage(
            __DIR__ .'/../../../static/js/jquery.js',
            __DIR__ .'/' . $this->getViewBridgeName() . '.js'
        );
    }
}
