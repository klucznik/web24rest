<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Employee;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/employee', name: 'api_employee_')]
class ApiEmployeeController extends AbstractController {

	public function __construct(private readonly ManagerRegistry $doctrine) {}

	#[Route('/', name: 'list', methods: ['get'])]
	public function list(): JsonResponse {
		$toReturn = [];

		foreach ($this->doctrine->getRepository(Employee::class)->findAll() as $employee) {
			if ($employee instanceof Employee) {
				$toReturn[] = $employee->toJsonArray();
			}
		}

		return $this->json($toReturn);
	}

	#[Route('/', name: 'create', methods: ['post'])]
	public function create(Request $request, ValidatorInterface $validator): JsonResponse {
		$employee = new Employee();
		$employee = $this->updateEmployeeFromRequest($request, $employee);

		$errors = $validator->validate($employee);
		if (count($errors) > 0) {
			return $this->json((string) $errors, 400);
		}

		$entityManager = $this->doctrine->getManager();
		$entityManager->persist($employee);
		$entityManager->flush();

		return $this->json($employee->toJsonArray());
	}

	#[Route('/{id}', name: 'get', methods: ['get'])]
	public function get(ManagerRegistry $doctrine, int $id): JsonResponse {
		$employee = $doctrine->getRepository(Employee::class)->find($id);

		if (!$employee) {
			return $this->json('No employee found for id ' . $id, 404);
		}

		return $this->json($employee->toJsonArray());
	}

	#[Route('/{id}', name: 'update', methods: ['put', 'patch'])]
	public function update(Request $request, int $id, ValidatorInterface $validator): JsonResponse {
		$entityManager = $this->doctrine->getManager();
		$employee = $entityManager->getRepository(Employee::class)->find($id);

		$employee = $this->updateEmployeeFromRequest($request, $employee);

		$errors = $validator->validate($employee);
		if (count($errors) > 0) {
			return $this->json((string) $errors, 400);
		}

		$entityManager->flush();

		return $this->json($employee->toJsonArray());
	}

	#[Route('/{id}', name: 'delete', methods: ['delete'])]
	public function delete(int $id): JsonResponse {
		$entityManager = $this->doctrine->getManager();
		$employee = $entityManager->getRepository(Employee::class)->find($id);

		if (!$employee) {
			return $this->json('No employee found for id ' . $id, 404);
		}

		$entityManager->remove($employee);
		$entityManager->flush();

		return $this->json('Deleted a employee with id ' . $id);
	}

	protected function updateEmployeeFromRequest(Request $request, Employee $employee): Employee {
		if ($request->request->has('firstname')) {
			$employee->setFirstname($request->request->get('firstname'));
		}

		if ($request->request->has('surname')) {
			$employee->setSurname($request->request->get('surname'));
		}

		if ($request->request->has('email')) {
			$employee->setEmail($request->request->get('email'));
		}

		if ($request->request->has('phone')) {
			$employee->setPhone($request->request->get('phone'));
		}

		if ($request->request->has('company')) {
			$companyId = $request->request->get('company');
			$company = $this->doctrine->getRepository(Company::class)->find($companyId);
			if ($company instanceof Company) {
				$employee->setCompany($company);
			}
		}

		return $employee;
	}
}
