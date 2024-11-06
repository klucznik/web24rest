<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[Assert\NotBlank]
	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[Assert\NotBlank]
	#[ORM\Column(length: 255)]
	private ?string $nip = null;

	#[Assert\NotBlank]
	#[ORM\Column(length: 255)]
	private ?string $address = null;

	#[Assert\NotBlank]
	#[ORM\Column(length: 255)]
	private ?string $city = null;

	#[Assert\NotBlank]
	#[ORM\Column(length: 64)]
	private ?string $postcode = null;

	#[OneToMany(targetEntity: Employee::class, mappedBy: "company")]
	private Collection $employees;

	public function __construct() {
		$this->employees = new ArrayCollection();
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getName(): ?string {
		return $this->name;
	}

	public function setName(string $name): static {
		$this->name = $name;

		return $this;
	}

	public function getNip(): ?string {
		return $this->nip;
	}

	public function setNip(string $nip): static {
		$this->nip = $nip;

		return $this;
	}

	public function getAddress(): ?string {
		return $this->address;
	}

	public function setAddress(string $address): static {
		$this->address = $address;

		return $this;
	}

	public function getCity(): ?string {
		return $this->city;
	}

	public function setCity(string $city): static {
		$this->city = $city;

		return $this;
	}

	public function getPostcode(): ?string {
		return $this->postcode;
	}

	public function setPostcode(string $postcode): static {
		$this->postcode = $postcode;

		return $this;
	}

	/**
	 * @return Collection<int, Employee>
	 */
	public function getEmployees(): Collection {
		return $this->employees;
	}

	public function toJsonArray(bool $includeRelations = true): array {
		$toReturn = [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'city' => $this->getCity(),
			'address' => $this->getAddress(),
			'nip' => $this->getNip(),
			'postcode' => $this->getPostcode(),
			'employees' => [],
		];

		foreach ($this->getEmployees() as $employee) {
			if ($includeRelations) {
				$toReturn['employees'][] = $employee->toJsonArray(false);
			} else {
				$toReturn['employees'][] = $employee->getId();
			}
		}

		return $toReturn;
	}
}
