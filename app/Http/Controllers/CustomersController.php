<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\SubscriptionsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class CustomersController extends Controller
{
    /**
     * Lists all customers.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return Response::json(Customer::all());
    }

    /**
     * Gets subscription data of a given customer.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function subscription(Request $request, $id): JsonResponse
    {
        $customer = Customer::find($id);

        $data = (new SubscriptionsService)->getSubscriptionsByCustomer($customer);

        return Response::json($data);
    }

    /**
     * Allow modifying customer's name.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $input = [$id, ...$request->all()];

        $request->validate([
            'name' => 'required|max:191',
            'id' => 'required|numeric|exists:customers,id'
        ], $input);

        $name = $request->get('name');

        $customer = Customer::find($id);
        $customer->name = $name;
        $customer->update();

        return Response::json($customer);
    }
}
