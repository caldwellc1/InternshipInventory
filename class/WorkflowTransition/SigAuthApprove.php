<?php
/**
 * This file is part of Internship Inventory.
 *
 * Internship Inventory is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Internship Inventory is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License version 3
 * along with Internship Inventory.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2011-2018 Appalachian State University
 */

namespace Intern\WorkflowTransition;
use Intern\WorkflowTransition;
use Intern\Internship;
use Intern\Exception\MissingDataException;

class SigAuthApprove extends WorkflowTransition {
    const sourceState = 'SigAuthReadyState';
    const destState   = 'SigAuthApprovedState';
    const actionName  = 'Approved by Signature Authority';

    public function getAllowedPermissionList()
    {
        return array('sig_auth_approve');
    }

    public function allowed(Internship $i)
    {
        // If international and not certified by OIED, then return false
        if($i->international == 1 && $i->oied_certified != 1){
            return false;
        }

        // Otherwise, check permissions as usual
        $perms = $this->getAllowedPermissionList();
        foreach($perms as $p){
            if(\Current_User::allow('intern', $p)){
                return true;
            }
        }
    }

    public function checkRequiredFields(Internship $i)
    {
        $emergName = $i->getEmergencyContactName();
        if(!isset($emergName)){
            throw new MissingDataException("Please add an emergency contact.");
        }
        
        /*
         * Prevents internship moving from signature authority approved to dean status without an address, city, and zip code.
         */
        if ($i->state == 'SigAuthReadyState' && isset($_POST['workflow_action']) && $_POST['workflow_action'] == 'Intern\WorkflowTransition\SigAuthApprove') {
            if (empty($_POST['loc_city']) || empty($_POST['loc_zip']) || empty($_POST['loc_address']) ||
                    (!$i->isDomestic() && empty($_POST['loc_province']))) {
                        throw new MissingDataException("This internship cannot continue to dean approval without a full physical location address.");
                }
        }
        
    }
}
