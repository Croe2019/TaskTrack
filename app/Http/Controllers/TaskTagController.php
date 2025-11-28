<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateTaskTagRequest;

class TaskTagController extends Controller
{
    private $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

}
