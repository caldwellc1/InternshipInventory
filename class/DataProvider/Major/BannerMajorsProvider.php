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

namespace Intern\DataProvider\Major;

use Intern\AcademicMajor;
use Intern\AcademicMajorList;

class BannerMajorsProvider extends MajorsProvider {

    protected $currentUserName;

    private $apiKey;

    public function __construct($currentUserName)
    {
        $this->currentUserName = $currentUserName;

        // Get the WSDL URI from module's settings
        $this->apiKey = \PHPWS_Settings::get('intern', 'wsdlUri');
        //$this->client = new \SoapClient($wsdlUri, array('WSDL_CACHE_MEMORY'));
    }

    public function getMajors($term): AcademicMajorList
    {
        if($term === null || $term == '') {
            throw new \InvalidArgumentException('Missing term.');
        }

        $termCode = $term->getTermCode();
        $params = array('Term' => $termCode, 'UserName' => $this->currentUserName);

        $url = 'sawarehouse.ess.appstate.edu/api/intern/majors/' . $termCode . '?username=intern&api_token=' . $this->apiKey;
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));
        $result = json_decode(curl_exec($curl));
        curl_close($curl);

        $results = $response->GetMajorInfoResult->MajorInfo;

        $majorsList = new AcademicMajorList();

        foreach($results as $major){
            // Makes sure the data from soap is an object
            if(!is_object($major)){
                continue;
            }
            // Skip majors/programs in University College
            else if($major->college_code === 'GC'){
                continue;
            }

            // Add it to the collection if it's not a duplicate
            $majorsList->addIfNotDuplicate(new AcademicMajor($major->major_code, $major->major_desc, $major->levl));
        }

        return $majorsList;
    }
}
