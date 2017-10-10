<?php
// src/Gosyl/CommonBundle/Entity/ParamUsers.php

namespace Gosyl\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Gosyl\FileserverBundle\Entity\FpDeleted;

/**
 * @author lippmann
 * 
 * @ORM\Table(name="common.PARAM_USERS", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"USERNAME"})})
 * @ORM\Entity(repositoryClass="Gosyl\CommonBundle\Entity\ParamUsersRepository")
 */
class ParamUsers implements AdvancedUserInterface, \Serializable {
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="common.PARAM_USERS_SEQ", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="USERNAME", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank(groups={"Inscription"}, message="Le nom d'utilisateur ne peut être vide")
     * @Assert\Length(min=3, max=255, minMessage="Le nom d'utilisateur doit au moins comporter 3 caractères", maxMessage="Le nom d'utilisateur ne peut pas comporter plus de 255 caractères")
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="PASSWORD", type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM", type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"Inscription"}, message="Le nom ne peut être vide")
     * @Assert\Length(max=255, maxMessage="Le nom ne peut pas comporter plus de 255 caractères")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="PRENOM", type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"Inscription"}, message="Le nom ne peut être vide")
     * @Assert\Length(max=255, maxMessage="Le prénom ne peut pas comporter plus de 255 caractères")
     */
    protected $prenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_NAISSANCE", type="datetime", nullable=false)
     */
    protected $dateNaissance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_INSCRIPTION", type="datetime", nullable=false)
     */
    protected $dateInscription;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=255, nullable=false)
     *
     * @Assert\Email(message="Adresse e-mail incorrecte. Format attendu : abc@domaine.xyz")
     * @Assert\Length(max="255", maxMessage="Email trop long")
     */
    protected $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_ACTIVE", type="boolean", nullable=false)
     */
    protected $isActive;
    
    /**
     * @var array
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    protected $roles;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_SUPPRESSION", type="datetime", nullable=true)
     * @Assert\DateTime(format="d/m/Y", message="Format invalide")
     */
    protected $dateSuppression;
    
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="LAST_ACTIVITY_AT", type="datetime", nullable=true)
     */
    protected $lastActivityAt;
    
    protected $salt;

    public function __construct() {
    	// De base, on va attribuer au nouveau utilisateur, le rôle « ROLE_USER »
    	$this->roles = array('ROLE_USER');
    	// Chaque utilisateur va se voir attribuer une clé permettant
    	// de saler son mot de passe. Cela n'est pas obligatoire,
    	// on pourrait mettre $salt à null
    	$this->salt = null;
    	$this->dateInscription = new \DateTime('now');
    	$this->setIsActive(0);
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id) {
    	$this->id = $id;
    	
    	return $this;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return ParamUsers
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return ParamUsers
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return ParamUsers
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->name;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return ParamUsers
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     * @return ParamUsers
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime 
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateInscription
     *
     * @param \DateTime $dateInscription
     * @return ParamUsers
     */
    public function setDateInscription($dateInscription)
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * Get dateInscription
     *
     * @return \DateTime 
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ParamUsers
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return ParamUsers
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set dateSuppression
     *
     * @param \DateTime $dateSuppression
     * @return ParamUsers
     */
    public function setDateSuppression($dateSuppression)
    {
        $this->dateSuppression = $dateSuppression;

        return $this;
    }

    /**
     * Get dateSuppression
     *
     * @return \DateTime 
     */
    public function getDateSuppression()
    {
        return $this->dateSuppression;
    }

    /**
     * Set role
     *
     * @param array
     * @return ParamUsers
     */
    public function setRoles($roles = array('ROLE_USER'))
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return role
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * Get Salt
     * 
     * @return string
     */
    public function getSalt() {
    	return null;//$this->salt;
    }
    
    public function eraseCredentials() {
    	// Ici nous n'avons rien à effacer.
    	// Cela aurait été le cas si nous avions un mot de passe en clair.
    }
    
    /** @see \Serializable::serialize() */
    public function serialize()
    {
    	return serialize(array(
    			$this->id,
    			$this->username,
    			$this->password,
    			// see section on salt below
    			// $this->salt,
    	));
    }
    
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
    	list (
    			$this->id,
    			$this->username,
    			$this->password,
    			// see section on salt below
    			// $this->salt
    			) = unserialize($serialized);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Security\Core\User\AdvancedUserInterface::isAccountNonExpired()
	 */
	public function isAccountNonExpired() {
		// TODO: Auto-generated method stub
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Security\Core\User\AdvancedUserInterface::isAccountNonLocked()
	 */
	public function isAccountNonLocked() {
		// TODO: Auto-generated method stub
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Security\Core\User\AdvancedUserInterface::isCredentialsNonExpired()
	 */
	public function isCredentialsNonExpired() {
		// TODO: Auto-generated method stub
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Security\Core\User\AdvancedUserInterface::isEnabled()
	 */
	public function isEnabled() {
		// TODO: Auto-generated method stub
		return $this->isActive;
	}
	
	/**
	 * On teste si le compte a été supprimé
	 */
	public function isAccountDeleted() {
		return is_null($this->dateSuppression);
	}
	
	/**
	 * Affiche le role pour le formulaire de modification
	 */
	public function getOldRole() {
		$aRole = $this->getRoles();
		
		return $aRole[0];
	}
	
	/**
	 * setter pour OldRole pour la compatibilité
	 */
	public function setOldRole($oldRole) {
		// nothing to do...
	}

	/**
	 * @return \DateTime
	 */
	public function getLastActivityAt() {
		return $this->lastActivityAt;
	}

	/**
	 *
	 * @param \DateTime $lastActivityAt
	 */
	public function setLastActivityAt(\DateTime $lastActivityAt) {
		$this->lastActivityAt = $lastActivityAt;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isActiveNow() {
		$delay = new \DateTime("2 minutes ago");
		
		return ($this->getLastActivityAt() > $delay);
	}
	
}
