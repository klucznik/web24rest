<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Company;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/company', name: 'api_company_')]
class ApiCompanyController extends AbstractController {

	public function __construct(private readonly ManagerRegistry $doctrine) {}

	#[Route('/', name: 'list', methods: ['get'])]
	public function list(): JsonResponse {
		$toReturn = [];

		foreach ($this->doctrine->getRepository(Company::class)->findAll() as $company) {
			if ($company instanceof Company) {
				$toReturn[] = $company->toJsonArray();
			}
		}

		return $this->json($toReturn);
	}

	#[Route('/', name: 'create', methods: ['post'])]
	public function create(Request $request, ValidatorInterface $validator): JsonResponse {
		$company = new Company();
		$company = $this->updateCompanyFromRequest($request, $company);

		$errors = $validator->validate($company);
		if (count($errors) > 0) {
			return $this->json((string) $errors, 400);
		}

		$entityManager = $this->doctrine->getManager();
		$entityManager->persist($company);
		$entityManager->flush();

		return $this->json($company->toJsonArray());
	}

	#[Route('/{id}', name: 'get', methods: ['get'])]
	public function get(int $id): JsonResponse {
		$company = $this->doctrine->getRepository(Company::class)->find($id);

		if (!$company) {
			return $this->json('No company found for id ' . $id, 404);
		}

		return $this->json($company->toJsonArray());
	}

	#[Route('/{id}', name: 'update', methods: ['put', 'patch'])]
	public function update(Request $request, int $id, ValidatorInterface $validator): JsonResponse {
		$entityManager = $this->doctrine->getManager();
		$company = $entityManager->getRepository(Company::class)->find($id);

		$company = $this->updateCompanyFromRequest($request, $company);

		$errors = $validator->validate($company);
		if (count($errors) > 0) {
			return $this->json((string) $errors, 400);
		}

		$entityManager->flush();

		return $this->json($company->toJsonArray());
	}

	#[Route('/{id}', name: 'delete', methods: ['delete'])]
	public function delete(int $id): JsonResponse {
		$entityManager = $this->doctrine->getManager();
		$company = $entityManager->getRepository(Company::class)->find($id);

		if (!$company) {
			return $this->json('No company found for id ' . $id, 404);
		}

		$entityManager->remove($company);
		$entityManager->flush();

		return $this->json('Deleted a company with id ' . $id);
	}

	protected function updateCompanyFromRequest(Request $request, Company $company): Company {
		if ($request->request->has('name')) {
			$company->setName($request->request->get('name'));
		}

		if ($request->request->has('city')) {
			$company->setCity($request->request->get('city'));
		}

		if ($request->request->has('address')) {
			$company->setAddress($request->request->get('address'));
		}

		if ($request->request->has('nip')) {
			$company->setNip($request->request->get('nip'));
		}

		if ($request->request->has('postcode')) {
			$company->setPostcode($request->request->get('postcode'));
		}

		return $company;
	}
}
