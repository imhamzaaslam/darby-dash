<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\CalendarFilterRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\CalendarFilterResource;

class CalendarFilterController extends Controller
{
    public function __construct(
        protected CalendarFilterRepositoryInterface $calendarFilterRepository
    ) {}

    public function index(Request $request)
    {
        $calendarFilters = $this->calendarFilterRepository->getAll();
        return CalendarFilterResource::collection($calendarFilters);
    }
}
