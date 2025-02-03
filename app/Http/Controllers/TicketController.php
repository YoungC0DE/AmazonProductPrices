<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tickets\CreateRequest;
use App\Http\Requests\Tickets\GetAllRequest;
use App\Repositories\TicketRepository;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function __construct(
        protected TicketRepository $repository
    ){}

    /**
     * @param string $ticketId
     * 
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
     * @param \App\Http\Requests\Tickets\GetAllRequest $request
     * 
     * @return JsonResponse
     */
    public function getAll(GetAllRequest $request)
    {
        try {
            $params = $request->all();

            $result = $this->repository->getAll($params);

            return $this->responseItem($result);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
