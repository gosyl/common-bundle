<?php
namespace Gosyl\CommonBundle\Twig;

class Datatable extends \Twig_Extension {
	/**
	 * Tableau contenant les paramètres des colonnes du dataTable
	 * @var array
	 */
	private $aCols = array();
	
	/**
	 * Tableau contenant les options pour le dataTable
	 * @var array
	 */
	private $aTableOptions = array();
	
	/**
	 * Active ou non le plugin Colvis
	 * @var boolean
	 */
	private $bColVis = false;
	
	/**
	 * Active ou non le header du tableau fixe lors du scroll
	 * @var boolean
	 */
	private $bFixedHeader = false;
	
	/**
	 * Résultat d'une requete Ajax ou non
	 * @var boolean
	 */
	private $bResultatAjax = false;
	
	/**
	 * Résultat vide ou non
	 * @var boolean
	 */
	private $bResultatVide = false;
	
	/**
	 * Active ou non le plugin d'export
	 * @var boolean
	 */
	private $bTableTools = false;
	
	/**
	 * Colonne de référence en cas de modification
	 * @var integer
	 */
	private $nColRef = 1;
	
	/**
	 * Chaine contenant le javascript d'initialisation du dataTable
	 * @var string
	 */
	private $sJsDataTable = '';
	
	/**
	 * Nom de l'objet javascript contenant les datas en Json
	 * @var string
	 */
	private $sNameDatas;
	
	/**
	 * Nom de l'objet javascript pour éviter les doublons
	 * @var string
	 */
	private $sNameTable;
	
	/**
	 * Chaine de retour contenant le dataTable en html
	 * @var string
	 */
	private $sTable = '';
	
	/**
	 * Variable contenant soit une chaine au format Json ou un tableau de résultats
	 * @var mixed
	 */
	private $xResultat;
	
	protected function _ajouteColonne() {
		$sColumns = '"columns": [';
		
		$bFirst = true;
		
		foreach ($this->aCols as $aValue) {
			$bFirst2 = true;
			
			if($bFirst) {
				$bFirst = false;
			} else {
				$sColumns .= ', ';
			}
			
			$sColumns .= '{';
			
			foreach ($aValue as $sKey => $xValue) {
				if($bFirst2) {
					$bFirst2 = false;
				} else {
					$sColumns .= ', ';
				}
				
				if(is_string($xValue) && $xValue != 'null') {
					$sColumns .= '"' . $sKey . '": "' . $xValue . '"';
				} elseif(is_string($xValue) && $xValue == 'null' && $sKey == 'data') {
					$sColumns .= '"' . $sKey . '": ' . $xValue;
				} elseif(is_bool($xValue)) {
					if($xValue) {
						$sColumns .= '"' . $sKey . '": true';
					} else {
						$sColumns .= '"' . $sKey . '": false';
					}
				} elseif(is_int($xValue)) {
					$sColumns .= '"' . $sKey . '": ' . $xValue;
				} elseif(is_array($xValue)) { // le type tableau permet d'ajouter une colonne action (édition, suppression)
					foreach($xValue as $sSubKey => $sValue) {
						$sColumns .= '"' . $sSubKey . '": ' . $sValue;
					}
				}
			}
			
			$sColumns .= '}';
		}
		
		$sColumns .= ']';
		return $sColumns;
	}
	
	protected function _ajouteJs() {
		$sJs = '<script type="text/javascript">
					$(document).ready(function() {
						var ' . $this->sNameTable . ' = $("#' . $this->sNameTable . '").dataTable({
							' . $this->_getOptions() . ',
							"lengthMenu": [[5 ,10, 25, 50, -1], [5, 10, 25, 50, "All"]],';
							if(count($this->aCols) > 15) {
								$sJs .= '
										"scrollX": "100%",
										"scrollY": "520",
										"scrollCollapse": true,
										';
							}
							
							if(!is_null($this->aCols) && !$this->bResultatAjax) {
								$sJs .= '
										"data": ' . $this->xResultat . ', 
										' . $this->_ajouteColonne() . '
										';
							}
							
							if($this->bResultatAjax) {
								$sJs .= '
										"processing": true,
										"serverSide": true,
										"ajax": {
											"type": "POST",
											"url": Gosyl.Common.rootPath + "' . $this->xResultat . '",
										},
										' . $this->_ajouteColonne() . ',
										';
							}
							
							//Gestion des plugins
							if($this->bColVis) {
								$sJs .= '
										"colVis": {
											"buttonText": "Ajouter/Supprimer des colonnes",
											"align": "left",
											"restore": true
										},
										';
							}
							
							if($this->bTableTools) {
								$sJs .= '
										"tableTools": {
											"sSwfPath": Gosyl.Common.basePath + "/js/Datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
											"sRowSelect": "single",
											"aButtons": [
												{
													"sExtends": "collection",
													"sButtonText": "Exporter",
													"aButtons": [
														{
														"sExtends": "pdf",
														"sButtonText": "En PDF"
														},
														{
														"sExtends": "csv",
														"sButtonText": "En CSV"
														},
														{
														"sExtends": "xls",
														"sButtonText": "En XLS"
														},
														{
														"sExtends": "copy",
														"sButtonText": "Dans le Presse-Papier"
														}
													]
												},
												{
													"sExtends" : "print",
													"sButtonText" : "Imprimer",
													"sInfo" : "<span style=\'text-align:center; font-size:3em;\'>Impression</span><br/><br/><br/><span style=\'font-size:1.5em;\'>Utilisez la fonction imprimer de votre navigateur.</span><br/><br/> <span style=\'font-size:2em;\'>Pour quitter, appuyez sur \'échap\' (esc)</span>"
												}
											]
										}
										';
							}
							
						$sJs .= '});
						';
						
						// Initialisation des plugins
						if($this->bColVis) {
							$sJs .= '
									var colvis' . $this->sNameTable . ' = new $.fn.dataTable.ColVis(' . $this->sNameTable . ');
									$(colvis'.$this->sNameTable . '.button()).insertAfter("div.info");
									';
						}
						
						//$sJs .= '//var colReorder'. $this->sNameTable . ' = new $.fn.dataTable.ColReorder(' . $this->sNameTable . ');';
						
						if($this->bTableTools) {
							$sJs .= 'var tableTools' . $this->sNameTable() . ' = new $.fn.dataTable.TableTools(' . $this->sNameTable() . ');
							$(tableTools' . $this->sNameTable() . '.fnContainer()).insertAfter("div.info");';
						}
						
						if($this->bFixedHeader && count($this->aCols()) <= 15) {
							$sJs .= '
								new FixedHeader('.$this->sNameTable().');';
						}
					$sJs .= '});
				</script>';
		
		return $sJs;
	}
	
	protected function _ajouteResultats() {
		$xResultats = $this->xResultat;
		
		$sReturn = '<script type="text/javascript">var '.$this->sNameDatas.' = \''.$xResultats.'\';</script>';
		
		return $sReturn;
	}
	
	protected function _createBody() {
		$xResultats = $this->xResultat;
	
		$sTable = '<tbody>';
	
		if(is_array($xResultats)) {
			foreach ($xResultats as $sKey => $aValue) {
				$sTable .= '<tr>';
				foreach ($aValue as $sSubKey => $sValue) {
					$sTable .= '<td>' . $sValue . '</td>';
				}
				$sTable .= '</tr>';
			}
		} elseif($this->bResultatAjax) {
			$sTable .= '<tr><td>Chargement des données en cours...</td></tr>';
		} else {
			$sTable .= '<tr><td>' . $xResultats . '</td></tr>';
		}
	
		$sTable .= '</tbody>';
	
		return $sTable;
	}
	
	protected function _creerTFoot() {
		$sTable = '<tfoot></tfoot>';
		
		return $sTable;
	}
	
	protected function _creerTHead() {
		$xResultats = $this->xResultat;
		
		$sTable = '<thead>';
		
		if(is_array($xResultats)) {
			$sTable .= '<tr>';
			foreach ($xResultats as $sKey => $sValue) {
				$sTable .= '<th>' . $sKey . '</th>';
			}
			$sTable .= '</tr>';
		} else {
			$sTable .= '<tr><th>-</th></tr>';
		}
		
		$sTable .= '</thead>';
		
		return $sTable;
	}
	
	protected function _getOptions($aTabOptions = null) {
		if(is_null($aTabOptions)) {
            $aTabOptions = $this->aTableOptions;
        }
        $bFirst = true;
        $sOption = '';
        foreach($aTabOptions as $skey => $value) {
            if($bFirst) {
                $bFirst = false;
            } else {
                $sOption .= ', ' . "\n";
            }
            if(is_string($value)) {
                $sOption .= '"' . $skey . '": "' . addslashes($value) .'"';
            } elseif(is_int($value)) {
                $sOption .= '"' . $skey . '": ' . $value;
            } elseif(is_array($value) && !(array_key_exists('function', $value))) {
                $sOption .= '"'.$skey.'": {' . "\n" . $this->_getOptions($value) . '}';
            } elseif(is_array($value) && (array_key_exists('function', $value) || array_key_exists('render', $value))) {
                $sOption .= '"' . $skey . '": ';
                $sOption .= $value[array_keys($value)[0]];
            }elseif(is_bool($value)) {
                $sOption .= '"'.$skey.'": ';
                $sOption .= $value ? 'true' : 'false';
            }
        }
        //$sOption .= ',';
        return $sOption;
	}
	
	protected function _setData($aData) {
		if(isset($aData['results']) && is_array($aData['results']) && count($aData['results']) > 0) { // Si les données sont présentes sous forme d'un tableau
			$this->xResultat = json_encode($aData['results']);
		} elseif(!isset($aData['cols']) && is_array($aData) && count($aData) > 0) {
			$this->xResultat = $aData;
		} elseif(is_string($aData['results']) && !empty($aData['results']) && !is_null($aData['results'])) {
			$this->xResultat = $aData['results'];
			$this->bResultatAjax = true;
		} else {
			$this->xResultat = 'Aucun résultat.';
			$this->bResultatVide = true;
		}
		
		if(is_array($aData) && isset($aData['cols'])) {
			$this->aCols = $aData['cols'];
		}
		
		if(isset($aData['options']) && is_array($aData['options'])) {
			$this->aTableOptions = $aData['options'];
		}
	}
	
	public function getFunctions() {
		return array(
				new \Twig_SimpleFunction('datatable', array($this, 'datatableFunction'), array('is_safe' => array('html')))
		);
	}
	
	public function datatableFunction($aData = array(), $bColVis = false, $bTableTools = false, $bFixedHeader = false, $sClassTable = 'table datatable', $nColReference = 0) {
		$this->nColRef = $nColReference;
		$this->bColVis = $bColVis;
		$this->bTableTools = $bTableTools;
		
		/**
		 * Génération d'un id aléatoire pour le dataTable et pour les données Json
		 */
		$this->sNameTable = 'oTable' . rand(0, 10000);
		$this->sNameDatas = 'jsonData' . rand(0, 10000);
		
		/**
		 * Récupération et mise en forme des données
		 */
		$this->_setData($aData);
		$sTable = $this->_ajouteJS();
		
		/**
		 * Début de création du tableau
		 */
		$sTable .= '<table id="'.$this->sNameTable.'" class="'.$sClassTable.'">';
		
		if(!is_null($this->xResultat) && is_string($this->xResultat) && !$this->bResultatVide) {
			$sTable .= $this->_ajouteResultats();
		} elseif(is_array($this->xResultat) && count($this->xResultat > 0)) {
			$sTable .= $this->_creerTHead();
			$sTable .= $this->_creerTBody();
			$sTable .= $this->_creerTFoot();
		} elseif(!$this->bResultatAjax) {
			$sTable = '<table class="table center thlarge">';
			$sTable .= $this->creerTBody();
			$sTable .= $this->creerTFoot();
		}
		
		$sTable .= '</table>';
		
		
		
		return $sTable;
	}
	
	public function getName() {
		return 'twig.extension.datatable';
	}
}