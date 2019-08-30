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

namespace Intern;

require_once PHPWS_SOURCE_DIR . 'mod/intern/vendor/autoload.php';

/**
 * InternshipContractPdfView
 *
 * View class for generating a PDF of an internship.
 *
 * @author jbooker
 * @package Intern
 */

class InternshipContractPdfView {

    private $internship;
    private $emergencyContacts;
    private $term;

    private $pdf;

    /**
     * Creates a new InternshipContractPdfView
     *
     * @param Internship $i
     * @param Array<EmergencyContact> $emergencyContacts
     * @param Term $term
     */
    public function __construct(Internship $i, Array $emergencyContacts, Term $term)
    {
        $this->internship = $i;
        $this->emergencyContacts = $emergencyContacts;
        $this->term = $term;

        $this->generatePdf();
    }

    /**
     * Returns the FPDI (FPDF) object which was generated by this view.
     *
     * @return FPDI
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Does the hard work of generating a PDF.
     */
    private function generatePdf()
    {
        $this->pdf = new \setasign\Fpdi\Fpdi('P', 'mm', 'Letter');
        $h = $this->internship->getHost();
        $s = $this->internship->getSupervisor();
        $d = $this->internship->getDepartment();
        $f = $this->internship->getFaculty();
        //$subject = $this->internship->getSubject();

        $this->pdf->setSourceFile(PHPWS_SOURCE_DIR . 'mod/intern/pdf/Contract_Updated_201920.pdf');
        $tplidx = $this->pdf->importPage(1);
        $this->pdf->addPage();
        $this->pdf->useTemplate($tplidx);

        $this->pdf->setFont('Times', null, 10);
        $this->pdf->setAutoPageBreak(true, 0);

        /**************************
         * Internship information *
        */

        /* Department */
        $this->pdf->setXY(138, 41);
        $this->pdf->multiCell(60, 3, $d->getName());

        /* Course title */
        $this->pdf->setXY(138, 53);
        $this->pdf->cell(73, 6, $this->internship->getCourseTitle());

        /* Location center aligned*/
        if($this->internship->isDomestic()){
            $this->pdf->setXY(85, 69);
            $this->pdf->cell(24, 5, 'X', 0, 0, 'C');
        }
        if($this->internship->isInternational()){
            $this->pdf->setXY(168, 69);
            $this->pdf->cell(24, 5, 'X', 0, 0, 'C');
        }

        /**
         * Student information.
         */
        $this->pdf->setXY(40, 84);
        $this->pdf->cell(55, 5, $this->internship->getFullName());

        $this->pdf->setXY(155, 84);
        $this->pdf->cell(42, 5, $this->internship->getBannerId());

        $this->pdf->setXY(41, 91);
        $this->pdf->cell(54, 5, $this->internship->getEmailAddress() . '@appstate.edu');

        $this->pdf->setXY(127, 91);
        $this->pdf->cell(54, 5, $this->internship->getPhoneNumber());


        /* Payment */
        if($this->internship->isPaid()){
            $this->pdf->setXY(25, 97);
            $this->pdf->cell(10,5, 'X');
        }else {
            $this->pdf->setXY(87, 97);
            $this->pdf->cell(10,5,'X');
        }

        // Stipend
        if($this->internship->hasStipend()) {
            $this->pdf->setXY(56, 97);
            $this->pdf->cell(10,5, 'X');
        }

        /* Hours */
        $this->pdf->setXY(190, 97);
        $this->pdf->cell(12, 5, $this->internship->getCreditHours());

        // Hours per week
        $this->pdf->setXY(147, 97);
        $this->pdf->cell(12, 5, $this->internship->getAvgHoursPerWeek());

        /* Term */
        $this->pdf->setXY(22, 103);
        $this->pdf->cell(27, 6, $this->term->getDescription());

        /* Dates for begining and end of term center aligned*/
        $this->pdf->setXY(109, 103);
        $this->pdf->cell(30, 5, $this->term->getStartDateFormatted(), 0, 0, 'C');
        $this->pdf->setXY(175, 103);
        $this->pdf->cell(30, 5, $this->term->getEndDateFormatted(), 0, 0, 'C');

        /***
         * Faculty supervisor information.
         */
        if(isset($f)){
            $this->pdf->setXY(28, 120);
            $this->pdf->cell(81, 5, $f->getFullName());

            $this->pdf->setXY(31, 127);
            $this->pdf->cell(81, 5, $f->getStreetAddress1());

            $this->pdf->setXY(16, 134);
            $this->pdf->cell(81, 5, $f->getStreetAddress2());

            $this->pdf->setXY(60, 134);
            $this->pdf->cell(81, 5, $f->getCity());

            $this->pdf->setXY(88, 134);
            $this->pdf->cell(81, 5, $f->getState());

            $this->pdf->setXY(95, 134);
            $this->pdf->cell(81, 5, $f->getZip());

            $this->pdf->setXY(29, 141);
            $this->pdf->cell(77, 5, $f->getPhone());

            $this->pdf->setXY(25, 148);
            $this->pdf->cell(77, 5, $f->getFax());

            $this->pdf->setXY(28, 155);
            $this->pdf->cell(77, 5, $f->getUsername() . '@appstate.edu');
        }

        /***
         * Host information.
        */
        $this->pdf->setXY(139, 117);
        $this->pdf->cell(71, 5, $h->getName());

        $host_address = $h->getStreetAddress();

        //TODO: make this smarter so it adds the line break between words
        if(strlen($host_address) < 49){
            // If it's short enough, just write it
            $this->pdf->setXY(127, 122);
            $this->pdf->cell(77, 5, $host_address);
        }else{
            // Too long, need to use two lines
            $hostLine1 = substr($host_address, 0, 49); // get first 50 chars
            $hostLine2 = substr($host_address, 49); // get the rest, hope it fits

            $this->pdf->setXY(127, 122);
            $this->pdf->cell(77, 5, $hostLine1);
            $this->pdf->setXY(113, 127);
            $this->pdf->cell(77, 5, $hostLine2);
        }

        /**
         * Supervisor info.
         */
        $this->pdf->setXY(113, 139);
        $super = "";
        $superName = $s->getSupervisorFullName();
        if(isset($superName) && !empty($superName) && $superName != ''){
            //test('ohh hai',1);
            $super .= $s->getSupervisorFullName();
        }

        $supervisorTitle = $s->getSupervisorTitle();

        if(isset($s->supervisor_title) && !empty($s->supervisor_title)){
            $super .= ', ' . $supervisorTitle;
        }
        $this->pdf->cell(75, 5, $super);

        $super_address = $s->getSuperAddress();
        //TODO: make this smarter so it adds the line break between words
        if(strlen($super_address) < 54){
            // If it's short enough, just write it
            $this->pdf->setXY(113, 144);
            $this->pdf->cell(78, 5, $super_address);
        }else{
            // Too long, need to use two lines
            $superLine1 = substr($super_address, 0, 54); // get first 55 chars
            $superLine2 = substr($super_address, 54); // get the rest, hope it fits

            $this->pdf->setXY(113, 144);
            $this->pdf->cell(78, 5, $superLine1);
            $this->pdf->setXY(113, 149);
            $this->pdf->cell(78, 5, $superLine2);
        }

        $this->pdf->setXY(125, 159);
        $this->pdf->cell(72, 5, $s->getSupervisorEmail());

        $this->pdf->setXY(125, 154);
        $this->pdf->cell(33, 5, $s->getSupervisorPhoneNumber());

        $this->pdf->setXY(166, 154);
        $this->pdf->cell(40, 5, $s->getSupervisorFaxNumber());

        /* Internship Location */
        $internshipAddress = trim($this->internship->getStreetAddress());
        $hostAddress = trim($s->getStreetAddress());


        /**********
         * Page 2 *
        **********/
        $tplidx = $this->pdf->importPage(2);
        $this->pdf->addPage();
        $this->pdf->useTemplate($tplidx);

        /* Emergency Contact Info */
        if(sizeof($this->emergencyContacts) > 0){
            $firstContact = $this->emergencyContacts[0];

            $this->pdf->setXY(60, 273);
            $this->pdf->cell(52, 0, $firstContact->getName());

            $this->pdf->setXY(134, 273);
            $this->pdf->cell(52, 0, $firstContact->getRelation());

            $this->pdf->setXY(173, 273);
            $this->pdf->cell(52, 0, $firstContact->getPhone());
        }
    }
}
