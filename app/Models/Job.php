<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    const STATUS_IN_PROGRESS = 'in-progress';
    const STATUS_NEW = 'new';
    const STATUS_FAILED = 'failed';
    const STATUS_SUCCESS = 'success';

    use HasFactory;

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function setError(string $error): self
    {
        $this->error = $error;
        return $this;
    }
}
