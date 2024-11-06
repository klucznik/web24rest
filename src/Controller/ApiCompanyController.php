<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Company;

#[Route('/api/company', name: 'api_company_')]
class ApiCompanyController extends AbstractController {

	#[Route('/', name: 'list', methods: ['get'])]
	public function list(ManagerRegistry $doctrine): JsonResponse {
		$toReturn = [];

		foreach ($doctrine->getRepository(Company::class)->findAll() as $company) {
			if ($company instanceof Company) {
				$toReturn[] = $company->toJsonArray();
			}
		}

		return $this->json($toReturn);
	}

	#[Route('/', name: 'create', methods: ['post'])]
	public function create(ManagerRegistry $doctrine, Request $request): JsonResponse {
		if (!$request->request->has('name')) {
			return $this->json('Missing company name', 400);
		}

		if (!$request->request->has('city')) {
			return $this->json('Missing company city', 400);
		}

		if (!$request->request->has('address')) {
			return $this->json('Missing company address', 400);
		}

		if (!$request->request->has('nip')) {
			return $this->json('Missing company nip', 400);
		}

		if (!$request->request->has('postcode')) {
			return $this->json('Missing company postcode', 400);
		}

		$entityManager = $doctrine->getManager();

		$company = new Company();
		$company->setName($request->request->get('name'));
		$company->setCity($request->request->get('city'));
		$company->setAddress($request->request->get('address'));
		$company->setNip($request->request->get('nip'));
		$company->setPostcode($request->request->get('postcode'));

		$entityManager->persist($company);
		$entityManager->flush();

		return $this->json($company->toJsonArray());
	}

	#[Route('/{id}', name: 'get', methods: ['get'])]
	public function get(ManagerRegistry $doctrine, int $id): JsonResponse {
		$company = $doctrine->getRepository(Company::class)->find($id);

		if (!$company) {
			return $this->json('No company found for id ' . $id, 404);
		}

		return $this->json($company->toJsonArray());
	}

	#[Route('/{id}', name: 'update', methods: ['put', 'patch'])]
	public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse {
		$entityManager = $doctrine->getManager();
		$company = $entityManager->getRepository(Company::class)->find($id);

		if (!$company) {
			return $this->json('No company found for id' . $id, 404);
		}

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

		$entityManager->flush();

		return $this->json($company->toJsonArray());
	}

	#[Route('/{id}', name: 'delete', methods: ['delete'])]
	public function delete(ManagerRegistry $doctrine, int $id): JsonResponse {
		$entityManager = $doctrine->getManager();
		$company = $entityManager->getRepository(Company::class)->find($id);

		if (!$company) {
			return $this->json('No company found for id ' . $id, 404);
		}

		$entityManager->remove($company);
		$entityManager->flush();

		return $this->json('Deleted a company with id ' . $id);
	}
}
