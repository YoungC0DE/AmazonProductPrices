<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tickets\CreateRequest;
use App\Repositories\TicketRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        protected TicketRepository $repository
    ){}

    /**
     * @param string $ticketId
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(string $ticketId)
    {
        try {
            $result = $this->repository->get($ticketId);

            return $this->responseItem($result);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param CreateRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(CreateRequest $request): JsonResponse
    {
        try {
            $result = $this->repository->create($request->all());

            return $this->responseItem($result);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        try {
            $pass = $request->get('securityPass', '');

            if (empty($pass) || $pass !== env('SECURITY_PASS')) {
                return $this->responseAccepted();
            }

            $result = $this->repository->getAll();

            return $this->responseItem($result);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
