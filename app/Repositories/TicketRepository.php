<?php

namespace App\Repositories;

use App\Models\Tickets;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;

class TicketRepository
{
    public function __construct(
        protected Tickets $model
    ){}

    /**
     * @param string $ticketId
     * @return App\Models\Tickets|null
     */
    public function get(string $ticketId)
    {
        return $this->model
            ->where('_id', new ObjectId($ticketId))
            ->first();
    }

    /**
     * @return App\Models\Tickets|null
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @param array $params
     * @return App\Models\Tickets|null
     */
    public function create($params)
    {
        $status = [
            'code' => $this->model::STATUS_PENDING,
            'name' => $this->model::STATUS_LABEL[$this->model::STATUS_PENDING]
        ];

        $data = [
            'crawlerType' => $params['crawlerType'],
            'options' => [
                'orderBy' => $params['options']['orderBy'],
                'totalPages' => $params['options']['totalPages']
            ],
            'status' => $status
        ];

        return $this->model->create($data);
    }

    /**
     * @param string $ticketId
     * @return void
     */
    public function delete(string $ticketId)
    {
        $this->model
            ->where('_id', new ObjectId($ticketId))
            ->delete();
    }

    /**
     * @param string $ticketId
     * @return array|null
     */
    public function getPendingTickets(string $ticketId = '')
    {
        $query = $this->model
            ->where('status.code', '=', $this->model::STATUS_PENDING);

        if (!empty($ticketId)) {
            $query->where('_id', new ObjectId($ticketId));
        }

        return $query->get()->toArray();
    }
}
