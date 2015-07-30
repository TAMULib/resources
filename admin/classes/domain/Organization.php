<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

class Organization extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}




	//returns number of children for this particular contact role
	public function getNumberOfChildren(){

		$query = "SELECT count(*) childCount FROM ResourceOrganizationLink WHERE organizationID = '" . $this->organizationID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		return $result['childCount'];

	}

	public function alreadyExists($shortName) {
		$query = "SELECT count(*) orgcount FROM Organization WHERE UPPER(shortName) = '" . str_replace("'", "''", strtoupper($shortName)) . "';";
		$result = $this->db->processQuery($query, 'assoc');
		return $result['orgcount'];
 	}

  	public function getOrganizationIDByName($shortName) {
    	$query = "SELECT organizationID FROM Organization WHERE UPPER(shortName) = '" . str_replace("'", "''", strtoupper($shortName)) . "';";
		$result = $this->db->processQuery($query, 'assoc');
		return $result['organizationID'];
  	}

	public function getIssues($archivedOnly=false) {
		$query = "SELECT i.* 
			  FROM Issue i
			  LEFT JOIN IssueRelationship ir ON (ir.issueID=i.issueID AND ir.entityTypeID=1)
			  WHERE ir.entityID={$this->primaryKey}";
		if ($archivedOnly) {
			$query .= " AND i.dateClosed IS NOT NULL";
		} else {
			$query .= " AND i.dateClosed IS NULL";
		}
		$query .= "	ORDER BY i.dateCreated DESC";
		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();
		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['issueID'])) {
			$object = new Issue(new NamedArguments(array('primaryKey' => $result['issueID'])));
			array_push($objects, $object);
		} else {
			foreach ($result as $row) {
				$object = new Issue(new NamedArguments(array('primaryKey' => $row['issueID'])));
				array_push($objects, $object);
			}
		}
		return $objects;
	}
}

?>
