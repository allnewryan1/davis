<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\PrincipalRepository")]
#[ORM\Table(name: 'principals')]
#[UniqueEntity('uri')]
class Principal
{
    public const PREFIX = 'principals/';

    public const READ_PROXY_SUFFIX = '/calendar-proxy-read';
    public const WRITE_PROXY_SUFFIX = '/calendar-proxy-write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\Unique]
    #[Assert\NotBlank]
    private $uri;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private $email;

    #[ORM\Column(name: 'displayname', type: 'string', length: 255, nullable: true)]
    private $displayName;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotBlank]
    private $isMain;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotBlank]
    private $isAdmin;

    #[ORM\ManyToMany(targetEntity: 'Principal')]
    #[ORM\JoinTable(name: 'groupmembers')]
    #[ORM\JoinColumn(name: 'principal_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'member_id', referencedColumnName: 'id')]
    private $delegees;

    public function __construct()
    {
        $this->delegees = new ArrayCollection();
        $this->isMain = true;
        $this->isAdmin = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUsername(): ?string
    {
        return str_replace(self::PREFIX, '', $this->getUri());
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return Collection|Principal[]
     */
    public function getDelegees(): Collection
    {
        return $this->delegees;
    }

    public function addDelegee(Principal $delegee): self
    {
        if (!$this->delegees->contains($delegee)) {
            $this->delegees[] = $delegee;
        }

        return $this;
    }

    public function removeDelegee(Principal $delegee): self
    {
        if ($this->delegees->contains($delegee)) {
            $this->delegees->removeElement($delegee);
        }

        return $this;
    }

    public function removeAllDelegees(): self
    {
        $this->delegees->clear();

        return $this;
    }

    public function getIsMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): self
    {
        $this->isMain = $isMain;

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }
}
