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

namespace Intern\WorkflowState;
use Intern\WorkflowState;
use Intern\Internship;
use Intern\WorkflowTransition\CreationTransition;

class CreationState extends WorkflowState {
    const friendlyName = 'New Internship';

    public function getTransitions(Internship $i)
    {
        $creationTrans = new CreationTransition();
        return array($creationTrans);
    }

    public function getAllowedPermissionList(){
        return array('create_internship', 'sig_auth_approve', 'dean_approve', 'grad_school_approve', 'register', 'oied_certify', 'distance_ed_register');
    }
}
