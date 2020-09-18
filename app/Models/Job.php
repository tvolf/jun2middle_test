<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string status
 * @property mixed|string error
 * @property mixed|string filename
 */
class Job extends Model
{
    use HasFactory;

    const STATUS_IN_PROGRESS = 'in-progress';
    const STATUS_NEW = 'new';
    const STATUS_FAILED = 'failed';
    const STATUS_SUCCESS = 'success';

    protected $fillable = [
        'filename',
        'status',
        'error'
    ];

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

    /**
     * @return mixed|string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed|string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return mixed|string
     */
    public function getError()
    {
        return $this->error;
    }
}
