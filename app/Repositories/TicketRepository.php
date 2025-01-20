<?php

namespace App\Repositories;

use App\Models\Tickets;
use MongoDB\BSON\ObjectId;

class TicketRepository
{
    protected $model;

    public function __construct() {
        $this->model = new Tickets();
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
            'code' => $this->model::STATUS_PENDING,
            'name' => $this->model::STATUS_LABEL[$this->model::STATUS_PENDING]
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

    /**
     * @param string $ticketId
     * @param array $data
     * 
     * @return void
     */
    public function updateTicketAsFinished(string $ticketId, array $data = [])
    {
        $data['status'] = [
            'code' => $this->model::STATUS_FINISHED,
            'text' => $this->model::STATUS_LABEL[$this->model::STATUS_FINISHED]
        ];

        $this->model->where('_id', new ObjectId($ticketId))
            ->update($data);
    }

    /**
     * @param string $ticketId
     * @param string $mgsError
     * 
     * @return void
     */
    public function updateTicketAsError(string $ticketId, string $mgsError = '')
    {
        $data['status'] = [
            'code' => $this->model::STATUS_ERROR,
            'text' => $this->model::STATUS_LABEL[$this->model::STATUS_ERROR],
            'errorMessage' => $mgsError
        ];

        $this->model->where('_id', new ObjectId($ticketId))
            ->update($data);
    }

    /**
     * @param string $ticketId
     * 
     * @return void
     */
    public function updateTicketAsRunning(string $ticketId)
    {
        $this->model->where('_id', new ObjectId($ticketId))
            ->update([
                'status' => 
                    [
                        'code' => $this->model::STATUS_RUNNING,
                        'text' => $this->model::STATUS_LABEL[$this->model::STATUS_RUNNING],
                    ]
            ]);
    }
}
