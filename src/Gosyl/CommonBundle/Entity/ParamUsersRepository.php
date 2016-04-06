<?php
namespace Gosyl\CommonBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Gosyl\CommonBundle\Business\DataTable;

class ParamUsersRepository extends EntityRepository {
	public function getAllForDataTable($aWhere = null, $aExtras = null) {
		$oDataTable = new DataTable();
		
		$aRequest = array(
				0 => 'U.id',
				1 => 'U.username',
				2 => 'U.isActive',
				3 => 'U.email',
				4 => 'U.roles',
				5 => 'U.name',
				6 => 'U.prenom',
				7 => 'U.dateInscription',
				8 => 'U.dateNaissance',
				9 => 'U.dateSuppression',
				10 => 'U.roles'
		);
		
		$aChamps = array();
		
		foreach ($aRequest as $value) {
			$aChamps[] = $value;
		}
		
		// Début de la requête
		$oQueryBuilder = $this->createQueryBuilder('U');
		$oQuery = $oQueryBuilder->select($aChamps);
		
		// Comptage des résultats
		$oQueryBuilderCount = $this->createQueryBuilder('U');
		$oQueryCount = $oQueryBuilderCount->select(array('COUNT(DISTINCT U.id) AS NB'));
		
		// Comptage des résultats sans filtre
		$oQueryBuilderCountTotal = $this->createQueryBuilder('U');
		$oQueryBuilderCountTotal->select(array('COUNT(DISTINCT U.id) AS NB'));
		
		$oQueryBuilder = $oDataTable->getExtraParams($oQueryBuilder, $aExtras, $aRequest);
		$oQueryBuilderCount = $oDataTable->getExtraParams($oQueryBuilderCount, $aExtras, $aRequest, true);
		
		/**
		 * @todo traiter le where
		 */
		if(!is_null($aWhere)) {
			foreach ($aWhere as $sCol => $aValue) {
				if($aValue['type'] == 'number') {
					$oQueryBuilder->where($oQueryBuilder->expr()->eq($sCol, $aValue['bind']))->setParameter($aValue['bind'], $aValue['val']);
					$oQueryBuilderCount->where($oQueryBuilder->expr()->eq($sCol, $aValue['bind']))->setParameter($aValue['bind'], $aValue['val']);
					$oQueryBuilderCountTotal->where($oQueryBuilder->expr()->eq($sCol, $aValue['bind']))->setParameter($aValue['bind'], $aValue['val']);
				}
			}
		}
		
		$oQueryCount = $oQueryBuilderCount->getQuery();
		$aNbRequest = $oQueryCount->getArrayResult();
		$nNbRequest = $aNbRequest[0]['NB'];
		
		$oQueryCountTotal = $oQueryBuilderCountTotal->getQuery();
		$aNbTotal = $oQueryCountTotal->getArrayResult();
		$nNbTotal = $aNbTotal[0]['NB'];
		
		$oQuery = $oQueryBuilder->getQuery();
		$aResultQuery = $oQuery->getArrayResult();
		
		$aResult = array('data' => $aResultQuery, 'recordsTotal' => $nNbRequest, 'recordsFiltered' => $nNbTotal);
		
		return $aResult;
	}
	
	public function modifierUsers($aDataToModifiy, $idUser) {
		$oQb = $this->createQueryBuilder('U');
		$oQb->update();
		
		foreach ($aDataToModifiy as $key => $value) {
			$aTabKey = explode('.', $key);
			$oQb->set($key, ':' . $aTabKey[1])
				->setParameter($aTabKey[1], $value);
		}
		
		$oQb->where($oQb->expr()->eq('U.id', $idUser));
		
		$iReturn = $oQb->getQuery()->execute();
		
		return $iReturn;
	}
	
	/**
	 * Désactive l'utilisateur et met à jour la date de suppression
	 * 
	 * @param int $idUser
	 * @param \DateTime $oDateNow
	 * @return mixed
	 */
	public function supprimerUtilisateur($idUser, \DateTime $oDateNow) {
		$oQb = $this->createQueryBuilder('U');
		
		$oQb->update()
			->set('U.dateSuppression', ':dateNow')
			->setParameter(':dateNow', $oDateNow)
			->where($oQb->expr()->eq('U.id', $idUser));
		
		$iReturn = $oQb->getQuery()->execute();
		
		return $iReturn;
	}
	
	/**
	 * Restaure un utilisateur
	 * @param int $idUser
	 * @return mixed
	 */
	public function restaureUtilisateur($idUser) {
		$oQb = $this->createQueryBuilder('U');
		
		$oQb->update()
			->set('U.dateSuppression', 'NULL')
			->where($oQb->expr()->eq('U.id', $idUser));
		
		$iReturn = $oQb->getQuery()->execute();
		
		return $iReturn;
	}
	
	/**
	 * Désactive un utilisateur
	 * 
	 * @param int $idUser
	 * @param int $activeOuDesactive
	 * @return mixed
	 */
	public function banUtilisateur($idUser, $activeOuDesactive) {
		$oQb = $this->createQueryBuilder('U');
	
		$oQb->update()
			->set('U.isActive', ':param')
			->where($oQb->expr()->eq('U.id', ':idUser'))
			->setParameter(':idUser', $idUser)
			->setParameter(':param', $activeOuDesactive);
	
		$iRetour = $oQb->getQuery()->execute();
	
		return $iRetour;
	}
	
	/**
	 * Récupère la liste des Administrateurs du site
	 * @return array
	 */
	public function getAdmin() {
		$oQB = $this->createQueryBuilder('U');
		$oQB->select(array('U.username', 'U.email'))
			->where($oQB->expr()->like('U.roles', $oQB->expr()->literal(serialize(array('ROLE_ADMIN')))));
	
		$oQuery = $oQB->getQuery();
		$oResult = $oQuery->getArrayResult();
	
		return $oResult;
	}
	
	/**
	 * Retourne les informations d'un utilisateur en fonction de son id
	 * 
	 * @param int $id
	 * @return array
	 */
	public function getUserById($id) {
		$oQB = $this->createQueryBuilder('U');
		$oQB->select()
			->where($oQB->expr()->eq('U.id', ':id'))
			->setParameter(':id', $id);
		
		$aResult = $oQB->getQuery()->getOneOrNullResult();
		
		return $aResult;
	}
	
	public function updatePassword($pwdEncoded, $idUser) {
		$oQb = $this->createQueryBuilder('U');
		
		$oQb->update()
			->set('U.password', ':pwd')
			->where($oQb->expr()->eq('U.id', ':idUser'))
			->setParameter(':pwd', $pwdEncoded)
			->setParameter(':idUser', $idUser);
		
		$iReturn = $oQb->getQuery()->execute();
		
		return $iReturn;
	}
}