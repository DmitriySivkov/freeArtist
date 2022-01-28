<?php


namespace App\Repositories;


use App\Models\Order;
use Carbon\Carbon;

class OrderRepository
{
    public function getList()
    {
        $query = Order::query();

        if (request()->has('filter')) {

            $filter = json_decode(request()->get('filter'), true);

            if ($filter['date']) {
                $filter['date'] = Carbon::parse($filter['date'])->format('Y-m-d');
                $query->whereDate('created_at', $filter['date']);
            }
        }

        return $query->get();
    }
}
