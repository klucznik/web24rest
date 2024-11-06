<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Employee;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/employee', name: 'api_employee_')]
class ApiEmployeeController extends AbstractController {

	#[Route('/', name: 'list', methods: ['get'])]
	public function list(ManagerRegistry $doctrine): JsonResponse {
		$toReturn = [];

		foreach ($doctrine->getRepository(Employee::class)->findAll() as $employee) {
			if ($employee instanceof Employee) {
				$toReturn[] = $employee->toJsonArray();
			}
		}

		return $this->json($toReturn);
	}

	#[Route('/', name: 'create', methods: ['post'])]
	public function create(ManagerRegistry $doctrine, Request $request): JsonResponse {
		if (!$request->request->has('firstname')) {
			return $this->json('Missing employee firstname', 400);
		}

		if (!$request->request->has('surname')) {
			return $this->json('Missing employee surname', 400);
		}

		if (!$request->request->has('email')) {
			return $this->json('Missing employee email', 400);
		}

		if (!$request->request->has('company')) {
			return $this->json('Missing employee company', 400);
		}

		$entityManager = $doctrine->getManager();

		$employee = new Employee();
		$employee->setFirstname($request->request->get('firstname'));
		$employee->setSurname($request->request->get('surname'));
		$employee->setEmail($request->request->get('email'));
		$employee->setPhone($request->request->get('phone'));

		$companyId = $request->request->get('company');
		$company = $doctrine->getRepository(Company::class)->find($companyId);
		if (!$company instanceof Company) {
			return $this->json('No company found for id ' . $companyId, 404);
		}
		$employee->setCompany($company);

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
	public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse {
		$entityManager = $doctrine->getManager();
		$employee = $entityManager->getRepository(Employee::class)->find($id);

		if (!$employee) {
			return $this->json('No employee found for id' . $id, 404);
		}

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
			$company = $doctrine->getRepository(Company::class)->find($companyId);
			if (!$company instanceof Company) {
				return $this->json('No company found for id ' . $companyId, 404);
			}
			$employee->setCompany($company);
		}

		$entityManager->flush();

		return $this->json($employee->toJsonArray());
	}

	#[Route('/{id}', name: 'delete', methods: ['delete'])]
	public function delete(ManagerRegistry $doctrine, int $id): JsonResponse {
		$entityManager = $doctrine->getManager();
		$employee = $entityManager->getRepository(Employee::class)->find($id);

		if (!$employee) {
			return $this->json('No employee found for id ' . $id, 404);
		}

		$entityManager->remove($employee);
		$entityManager->flush();

		return $this->json('Deleted a employee with id ' . $id);
	}
}
