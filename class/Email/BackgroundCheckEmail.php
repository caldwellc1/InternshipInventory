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

namespace Intern\Email;
use \Intern\Internship;
use \Intern\InternSettings;
use \Intern\Term;
use \Intern\TermFactory;

/**
 * Generates an email to the background check coordinator, notifying
 * them to create/setup a background check and/or drug test for this student's internship.
 * Generally used when someone checks the "background check required" or "drug test needed"
 * fields on the Edit Internship interface.
 *
 * @author jbooker
 * @package Intern
 */
class BackgroundCheckEmail extends Email{

    private $internship;
    private $term;
    private $host;

    private $backgroundCheck;
    private $drugCheck;

    /**
    * Sends the Background or Drug check notification email.
    *
    * @param InternSettings $emailSettings
    * @param Internship $internship
    * @param Term $term
    * @param bool $backgroundCheck
    * @param bool $drugCheck
    */
    public function __construct(InternSettings $emailSettings, Internship $internship, Term $term, Host $host, $backgroundCheck, $drugCheck) {
        parent::__construct($emailSettings);

        $this->internship = $internship;
        $this->term = $term;
        $this->host = $host;
        $this->backgroundCheck = $backgroundCheck;
        $this->drugCheck = $drugCheck;
    }

    protected function getTemplateFileName() {
        return 'email/BackgroundDrugCheck.tpl';
    }

    protected function buildMessage()
    {
        $this->to = explode(',', $this->emailSettings->getBackgroundCheckEmail());

        $this->tpl['NAME'] = $this->internship->getFullName();
        $this->tpl['BANNER'] = $this->internship->banner;
        $this->tpl['TERM'] = $this->term->getDescription();
        $this->tpl['LEVEL'] = $this->internship->getLevel();
        $this->tpl['EMAIL'] = $this->internship->getEmailAddress() . $this->emailSettings->getEmailDomain();
        $this->tpl['HOST'] = $this->host->getName();

        if ($this->internship->getFaculty() !== null) {
            $this->tpl['FACULTY'] = $this->internship->getFaculty();
        } else {
            $this->tpl['FACULTY'] = 'Faculty supervisor not set.';
        }

        if ($this->backgroundCheck === true && $this->drugCheck === true) {
            $this->subject = 'Internship Background & Drug Check Needed for ' . $this->internship->getFullName();
            $this->tpl['CHECK'] = 'Background & Drug';
        } else if ($this->backgroundCheck === true) {
            $this->subject = 'Internship Background Check Needed for ' . $this->internship->getFullName();
            $this->tpl['CHECK'] = 'Background';
        } else if ($this->drugCheck === true) {
            $this->subject = 'Internship Drug Test Needed for ' . $this->internship->getFullName();
            $this->tpl['CHECK'] = 'Drug';
        } else {
            // Both variables were false, what are we doing here?
            throw new \InvalidArgumentException( 'Both background check and drug test varaibles were false. One or both must be set to true.');
        }
    }
}
