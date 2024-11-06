<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[Assert\NotBlank]
	#[ORM\Column(length: 255)]
	private ?string $firstname = null;

	#[Assert\NotBlank]
	#[ORM\Column(length: 255)]
	private ?string $surname = null;

	#[Assert\NotBlank]
	#[ORM\Column(length: 255)]
	private ?string $email = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $phone = null;

	#[Assert\NotBlank]
	#[ORM\ManyToOne(targetEntity: Company::class)]
	private Company $company;

	public function getId(): ?int {
		return $this->id;
	}

	public function getFirstname(): ?string {
		return $this->firstname;
	}

	public function setFirstname(string $firstname): static {
		$this->firstname = $firstname;

		return $this;
	}

	public function getSurname(): ?string {
		return $this->surname;
	}

	public function setSurname(string $surname): static {
		$this->surname = $surname;

		return $this;
	}

	public function getEmail(): ?string {
		return $this->email;
	}

	public function setEmail(string $email): static {
		$this->email = $email;

		return $this;
	}

	public function getPhone(): ?string {
		return $this->phone;
	}

	public function setPhone(?string $phone): static {
		$this->phone = $phone;

		return $this;
	}

	public function getCompany(): ?Company {
		return $this->company;
	}

	public function setCompany(?Company $company): static {
		$this->company = $company;

		return $this;
	}

	public function toJsonArray(bool $includeRelations = true): array {
		$toReturn = [
			'id' => $this->getId(),
			'firstname' => $this->getFirstname(),
			'surname' => $this->getSurname(),
			'email' => $this->getEmail(),
			'phone' => $this->getPhone(),
			'company' => $this->getCompany()->getId()
		];

		if ($includeRelations) {
			$toReturn['company'] = $this->getCompany()->toJsonArray(false);
		}

		return $toReturn;
	}
}
