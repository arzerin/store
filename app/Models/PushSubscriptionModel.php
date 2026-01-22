<?php

namespace App\Models;

use CodeIgniter\Model;

class PushSubscriptionModel extends Model
{
    protected $table            = 'push_subscriptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'endpoint'    => 'required',
        'public_key'  => 'required',
        'auth_token'  => 'required',
    ];

    protected $validationMessages = [
        'endpoint' => [
            'required' => 'Endpoint is required',
        ],
        'public_key' => [
            'required' => 'Public key is required',
        ],
        'auth_token' => [
            'required' => 'Auth token is required',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Find subscription by endpoint
     */
    public function findByEndpoint(string $endpoint)
    {
        return $this->where('endpoint', $endpoint)->first();
    }

    /**
     * Get all active subscriptions
     */
    public function getAllSubscriptions()
    {
        return $this->findAll();
    }
}
