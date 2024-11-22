<?php

namespace App\Repositories;

use App\Models\Tickets;
use MongoDB\BSON\ObjectId;

class TicketRepository
{
    public function __construct(
        protected Tickets $model
    ) {

    }

    /**
     * @param string $ticketId
     * @return mixed
     */
    public function get(string $ticketId)
    {
        return $this->model
            ->where('_id', new ObjectId($ticketId))
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @param $params
     * @return mixed
     */
    public function create($params)
    {
        $statusData = [
            'code' => Tickets::STATUS_PENDING,
            'name' => Tickets::STATUS_LABEL[Tickets::STATUS_PENDING]
        ];

        $data = [
          'requestSettings' => $params['requestSettings'],
          'filters' => $params['filters'],
          'status' => $statusData
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
