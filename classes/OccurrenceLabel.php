<?php
include_once($serverRoot.'/config/dbconnection.php');

class OccurrenceLabel{

	private $conn;
	private $collid;
	private $collArr = array();

	private $errorArr = array();

	public function __construct(){
 		$this->conn = MySQLiConnectionFactory::getCon("write");
	}

	public function __destruct(){
		if(!($this->conn === null)) $this->conn->close();
	}

	//Label functions
	public function queryOccurrences($postArr){
		$retArr = array();
		if($this->collid){
			$sqlWhere = '';
			$sqlOrderBy = '';
			if($postArr['labelproject']){
				$sqlWhere .= 'AND (labelproject = "'.$this->cleanInStr($postArr['labelproject']).'") ';
			}
			if($postArr['recordenteredby']){
				$sqlWhere .= 'AND (recordenteredby = "'.$this->cleanInStr($postArr['recordenteredby']).'") ';
			}
			$date1 = $this->cleanInStr($postArr['date1']);
			$date2 = $this->cleanInStr($postArr['date2']);
			if(!$date1 && $date2){
				$date1 = $date2;
				$date2 = '';
			}
			$dateTarget = $this->cleanInStr($postArr['datetarget']);
			if($date1){
				$dateField = 'dateentered';
				if($date2){
					$sqlWhere .= 'AND (DATE('.$dateTarget.') BETWEEN "'.$date1.'" AND "'.$date2.'") ';
				}
				else{
					$sqlWhere .= 'AND (DATE('.$dateTarget.') = "'.$date1.'") ';
				}
				
				$sqlOrderBy .= ','.$dateTarget;
			}
			$rnIsNum = false;
			if($postArr['recordnumber']){
				$rnArr = explode(',',$this->cleanInStr($postArr['recordnumber']));
				$rnBetweenFrag = array();
				$rnInFrag = array();
				foreach($rnArr as $v){
					$v = trim($v);
					if($p = strpos($v,' - ')){
						$term1 = trim(substr($v,0,$p));
						$term2 = trim(substr($v,$p+3));
						if(is_numeric($term1) && is_numeric($term2)){
							$rnIsNum = true;
							$rnBetweenFrag[] = '(recordnumber BETWEEN '.$term1.' AND '.$term2.')';
						}
						else{
							$catTerm = 'recordnumber BETWEEN "'.$term1.'" AND "'.$term2.'"';
							if(strlen($term1) == strlen($term2)) $catTerm .= ' AND length(recordnumber) = '.strlen($term2); 
							$rnBetweenFrag[] = '('.$catTerm.')';
						}
					}
					else{
						$rnInFrag[] = $v;
					}
				}
				$rnWhere = '';
				if($rnBetweenFrag){
					$rnWhere .= 'OR '.implode(' OR ',$rnBetweenFrag);
				}
				if($rnInFrag){
					$rnWhere .= 'OR (recordnumber IN("'.implode('","',$rnInFrag).'")) ';
				}
				$sqlWhere .= 'AND ('.substr($rnWhere,3).') ';
			}
			if($postArr['recordedby']){
				$sqlWhere .= 'AND (recordedby LIKE "%'.$this->cleanInStr($postArr['recordedby']).'%") ';
				$sqlOrderBy .= ',(recordnumber'.($rnIsNum?'+1':'').')';
			}
			if($postArr['identifier']){
				$iArr = explode(',',$this->cleanInStr($postArr['identifier']));
				$iBetweenFrag = array();
				$iInFrag = array();
				foreach($iArr as $v){
					$v = trim($v);
					if($p = strpos($v,' - ')){
						$term1 = trim(substr($v,0,$p));
						$term2 = trim(substr($v,$p+3));
						if(is_numeric($term1) && is_numeric($term2)){
							$searchIsNum = true; 
							$iBetweenFrag[] = '(catalogNumber BETWEEN '.$term1.' AND '.$term2.')';
						}
						else{
							$catTerm = 'catalogNumber BETWEEN "'.$term1.'" AND "'.$term2.'"';
							if(strlen($term1) == strlen($term2)) $catTerm .= ' AND length(catalogNumber) = '.strlen($term2); 
							$iBetweenFrag[] = '('.$catTerm.')';
						}
					}
					else{
						$iInFrag[] = $v;
					}
				}
				$iWhere = '';
				if($iBetweenFrag){
					$iWhere .= 'OR '.implode(' OR ',$iBetweenFrag);
				}
				if($iInFrag){
					$iWhere .= 'OR (catalogNumber IN("'.implode('","',$iInFrag).'")) ';
				}
				$sqlWhere .= 'AND ('.substr($iWhere,3).') ';
				$sqlOrderBy .= ',catalogNumber';
			}
			if($sqlWhere){
				$sql = 'SELECT occid, IFNULL(duplicatequantity,1) AS q, CONCAT_WS(" ",recordedby,IFNULL(recordnumber,eventdate)) AS collector, '.
					'family, sciname, CONCAT_WS("; ",country, stateProvince, county, locality) AS locality '.
					'FROM omoccurrences '.($postArr['recordedby']?'use index(Index_collector) ':'').
					'WHERE collid = '.$this->collid.' '.$sqlWhere;
				if($this->collArr['colltype'] == 'General Observations') $sql .= ' AND observeruid = '.$GLOBALS['SYMB_UID'];
				//if($sqlOrderBy) $sql .= ' ORDER BY '.substr($sqlOrderBy,1);
				$sql .= ' LIMIT 400';
				//echo '<div>'.$sql.'</div>';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$occId = $r->occid;
					$retArr[$occId]['q'] = $r->q;
					$retArr[$occId]['c'] = $r->collector;
					//$retArr[$occId]['f'] = $r->family;
					$retArr[$occId]['s'] = $r->sciname;
					$retArr[$occId]['l'] = $r->locality;
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function getLabelArray($occidArr, $speciesAuthors){
		$retArr = array();
		if($occidArr){
			$authorArr = array();
			$sqlWhere = 'WHERE (o.occid IN('.implode(',',$occidArr).')) ';
			if($this->collArr['colltype'] == 'General Observations') $sqlWhere .= 'AND o.observeruid = '.$GLOBALS['SYMB_UID'].' ';
			//Get species authors for infraspecific taxa
			$sql1 = 'SELECT o.occid, t2.author '.
				'FROM taxa t INNER JOIN omoccurrences o ON t.tid = o.tidinterpreted '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'INNER JOIN taxa t2 ON ts.parenttid = t2.tid '.
				$sqlWhere.' AND t.rankid > 220 AND ts.taxauthid = 1 ';
			if(!$speciesAuthors){
				$sql1 .= 'AND t.unitname2 = t.unitname3 ';
			}
			//echo $sql1; exit;
			if($rs1 = $this->conn->query($sql1)){
				while($row1 = $rs1->fetch_object()){
					$authorArr[$row1->occid] = $row1->author;
				}
				$rs1->free();
			}
				
			//Get occurrence records
			$sql2 = 'SELECT o.occid, o.collid, o.catalognumber, o.othercatalognumbers, '.
				'o.family, o.sciname AS scientificname, o.genus, o.specificepithet, o.taxonrank, o.infraspecificepithet, '.
				'o.scientificnameauthorship, "" AS parentauthor, o.identifiedby, o.dateidentified, o.identificationreferences, '.
				'o.identificationremarks, o.taxonremarks, o.identificationqualifier, o.typestatus, o.recordedby, o.recordnumber, o.associatedcollectors, '.
				'DATE_FORMAT(o.eventdate,"%e %M %Y") AS eventdate, o.year, o.month, o.day, DATE_FORMAT(o.eventdate,"%M") AS monthname, '.
				'o.verbatimeventdate, o.habitat, o.substrate, o.occurrenceremarks, o.associatedtaxa, o.verbatimattributes, '.
				'o.reproductivecondition, o.cultivationstatus, o.establishmentmeans, o.country, '.
				'o.stateprovince, o.county, o.municipality, o.locality, o.decimallatitude, o.decimallongitude, '.
				'o.geodeticdatum, o.coordinateuncertaintyinmeters, o.verbatimcoordinates, '.
				'o.minimumelevationinmeters, o.maximumelevationinmeters, '.
				'o.verbatimelevation, o.disposition, o.duplicatequantity, o.datelastmodified '.
				'FROM omoccurrences o '.$sqlWhere;
			//echo 'SQL: '.$sql2;
			if($rs2 = $this->conn->query($sql2)){
				while($row2 = $rs2->fetch_assoc()){
					$row2 = array_change_key_case($row2);
					if(array_key_exists($row2['occid'],$authorArr)){
						$row2['parentauthor'] = $authorArr[$row2['occid']];
					}
					$retArr[$row2['occid']] = $row2;
				}
				$rs2->free();
			}
		}
		return $retArr;
	}

	public function getLabelProjects(){
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT DISTINCT labelproject, observeruid '.
				'FROM omoccurrences '.
				'WHERE labelproject IS NOT NULL AND collid = '.$this->collid.' ';
			if($this->collArr['colltype'] == 'General Observations') $sql .= 'AND observeruid = '.$GLOBALS['SYMB_UID'].' ';
			$sql .= 'ORDER BY labelproject';
			$rs = $this->conn->query($sql);
			$altArr = array();
			while($r = $rs->fetch_object()){
				if($GLOBALS['SYMB_UID'] == $r->observeruid){
					$retArr[] = $r->labelproject;
				}
				else{
					$altArr[] = $r->labelproject;
				}
			}
			$rs->free();
			if($altArr){
				if($retArr) $retArr[] = '------------------';
				$retArr = array_merge($retArr,$altArr);
			}
		}
		return $retArr;
	}

	public function getDatasetProjects(){
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT DISTINCT ds.datasetid, ds.name '.
				'FROM omoccurdatasets ds INNER JOIN userroles r ON ds.datasetid = r.tablepk '.
				'INNER JOIN omoccurdatasetlink dl ON ds.datasetid = dl.datasetid '.
				'INNER JOIN omoccurrences o ON dl.occid = o.occid '.
				'WHERE (r.tablename = "omoccurdatasets") AND (o.collid = '.$this->collid.') ';
			if($this->collArr['colltype'] == 'General Observations') $sql .= 'AND o.observeruid = '.$GLOBALS['SYMB_UID'].' ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->datasetid] = $r->name;
			}
			$rs->free();
		}
		return $retArr;
	}

	//General functions
	public function exportCsvFile($postArr, $speciesAuthors){
		global $charset;
		$occidArr = $postArr['occid'];
		if($occidArr){
			$labelArr = $this->getLabelArray($occidArr, $speciesAuthors);
			if($labelArr){
				$fileName = 'labeloutput_'.time().".csv";
				header('Content-Description: Symbiota Label Output File');
				header ('Content-Type: text/csv');
				header ('Content-Disposition: attachment; filename="'.$fileName.'"'); 
				header('Content-Transfer-Encoding: '.strtoupper($charset));
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				
				$fh = fopen('php://output','w');
				$headerArr = array("occid","catalogNumber","otherCatalogNumbers","family","scientificName","genus","specificEpithet",
					"taxonRank","infraSpecificEpithet","scientificNameAuthorship","parentAuthor","identifiedBy",
					"dateIdentified","identificationReferences","identificationRemarks","taxonRemarks","identificationQualifier",
		 			"typeStatus","recordedBy","recordNumber","associatedCollectors","eventDate","year","month","monthName","day",
			 		"verbatimEventDate","habitat","substrate","verbatimAttributes","occurrenceRemarks",
		 			"associatedTaxa","reproductiveCondition","establishmentMeans","country",
		 			"stateProvince","county","municipality","locality","decimalLatitude","decimalLongitude",
			 		"geodeticDatum","coordinateUncertaintyInMeters","verbatimCoordinates",
		 			"minimumElevationInMeters","maximumElevationInMeters","verbatimElevation","disposition");
				fputcsv($fh,$headerArr);
				//change header value to lower case
				$headerLcArr = array();
				foreach($headerArr as $k => $v){
					$headerLcArr[strtolower($v)] = $k;
				}
				//Output records
				foreach($labelArr as $occid => $occArr){
					$dupCnt = $postArr['q-'.$occid];
					for($i = 0;$i < $dupCnt;$i++){
						fputcsv($fh,array_intersect_key($occArr,$headerLcArr));
					}
				}
				fclose($fh);
			}
			else{
				echo "Recordset is empty.\n";
			}
		}
	}

	//General setters and getters
	public function setCollid($collid){
		if(is_numeric($collid)){
			$this->collid = $collid;
			$this->setCollMetadata();
		}
	}
	
	public function getCollName(){
		return $this->collArr['collname'].' ('.$this->collArr['instcode'].($this->collArr['collcode']?':'.$this->collArr['collcode']:'').')';
	}

	public function getMetaDataTerm($key){
		if(!$this->collArr) return;
		if($this->collArr && array_key_exists($key,$this->collArr)){
			return $this->collArr[$key];
		}
	}

	private function setCollMetadata(){
		if($this->collid){
			$sql = 'SELECT institutioncode, collectioncode, collectionname, colltype '.
				'FROM omcollections WHERE collid = '.$this->collid;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$this->collArr['instcode'] = $r->institutioncode;
					$this->collArr['collcode'] = $r->collectioncode;
					$this->collArr['collname'] = $r->collectionname;
					$this->collArr['colltype'] = $r->colltype;
				}
				$rs->free();
			}
		}
	}

	public function getErrorArr(){
		return $this->errorArr;
	}
	
	//Misc functions
	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>