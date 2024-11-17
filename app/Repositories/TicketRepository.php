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
     * @return App\Models\Tickets|null
     */
    public function create()
    {
        $status = [
            'code' => $this->model::STATUS_PENDING,
            'name' => $this->model::STATUS_LABEL[$this->model::STATUS_PENDING]
        ];

        $params = [
            'request' => 'teste',
            'status' => $status
        ];

        return $this->model->create($params);
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
     * @return App\Models\Tickets|null
     */
    public function getPendingTickets(string $ticketId = '')
    {
        $query = $this->model
            ->where('status.code', '=', $this->model::STATUS_PENDING);

        if (!empty($ticketId)) {
            $query->where('_id', new ObjectId($ticketId));
        }

        return $query->get();
    }
}