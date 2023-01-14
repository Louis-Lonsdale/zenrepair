<?php

/**
 * 
 */

declare(strict_types = 1);

namespace App\Models;

class TicketModel extends BaseModel
{
    public function createTicket()
    {

    }

    public function getTicket(string $id)
    {
        $this->sql = 'SELECT * FROM zenrepair.tickets WHERE (id = :id)';

        $this->stmt = $this->database->prepareStatement($this->sql);

        $this->stmt->bindValue(':id', $id);

        $this->stmt->execute();

        $this->result = $this->database->fetchAllRows($this->stmt);

        return $this->result;
    }

    public function getAllTickets() : array
    {
        $this->sql = 'SELECT * FROM zenrepair.tickets ORDER BY updated DESC';

        $this->stmt = $this->database->prepareStatement($this->sql);
        $this->stmt->execute();

        $this->result = $this->database->fetchAllRows($this->stmt);

        return $this->result;
    }

    public function updateTicket()
    {

    }

    public function deleteTicket()
    {

    }
}