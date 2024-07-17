<?php

// src/Controller/CountController.php

namespace App\Controller;

use App\Service\LogCountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CountController extends AbstractController
{
    private $logCountService;

    public function __construct(LogCountService $logCountService)
    {
        $this->logCountService = $logCountService;
    }

    #[Route('/count', name: 'count', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            // Extract filters from the request query parameters
            $filters = [
                'serviceNames' => $request->query->get('serviceNames'),
                'statusCode' => $request->query->get('statusCode'),
                'startDate' => $request->query->get('startDate'),
                'endDate' => $request->query->get('endDate'),
            ];

            // Call the service to get the count
            $count = $this->logCountService->countLogs($filters);

            // Return JSON response with the count
            return new JsonResponse(['count' => $count]);

        } catch (\Exception $e) {
            return new JsonResponse('Error retrieving count' . $e->getMessage(), 500);
        }
    }
}
