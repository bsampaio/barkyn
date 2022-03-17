<?php

namespace App\Http\Controllers;

use App\Exceptions\CantUpdateSubscriptionPrices;
use App\Exceptions\SubscriptionMinimumAmountOfPetsNotReached;
use App\Exceptions\UserAlreadySubscripted;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Subscription;
use App\Services\SubscriptionsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class SubscriptionsController extends Controller
{
    const THE_CUSTOMER_HASN_T_AN_ACTIVE_SUBSCRIPTION = 'The customer hasn\'t an active subscription.';
    /**
     * @var SubscriptionsService
     */
    private $subscriptionService;

    public function __construct()
    {
        $this->subscriptionService = new SubscriptionsService();
    }

    /**
     * @param Request $request
     * @param $customerId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request, $customerId): JsonResponse
    {
        $input = [
            'customer_id' => $customerId,
        ];

        $request->request->add($input);
        $genders = join(',', Pet::getGenders());
        $lifestages = "/Puppy|Adult|Senior|^\d+ kg$/";

        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id',
            'pets' => 'required|array|size:1',
            'pets.*.name' => 'required',
            'pets.*.gender' => 'required|in:' . $genders,
            'pets.*.lifestage' => ['required', 'regex:' . $lifestages]
        ]);

        $customer = Customer::find($customerId);
        $pet = new Pet();
        $pet->fill($request->input('pets.0'));

        //Add subscription
        try {
            $subscription = $this->subscriptionService->createSubscription($customer);
            $this->subscriptionService->addPetToSubscription($subscription, $pet);

        } catch (UserAlreadySubscripted $e) {
            return response()->json([
                'message' => 'Customer already have a subscription. Try to add pets.'
            ], 422);
        } catch (CantUpdateSubscriptionPrices $e) {
            return response()->json([
                'message' => 'There was a problem saving prices.'
            ], 422);
        } catch (SubscriptionMinimumAmountOfPetsNotReached $e) {
            return response()->json([
                'message' => 'At least one pet is needed to activate de subscription.'
            ], 422);
        }

        return response()->json([
            'message' => 'Subscription created successfully.'
        ]);
    }

    /**
     * Retrieves pets from subscription
     *
     * @param Request $request
     * @param $customerId
     * @return JsonResponse
     */
    public function pets(Request $request, $customerId): JsonResponse
    {
        $customer = Customer::find($customerId);
        if(!$customer) {
            return response()->json([
                'message' => 'Customer not found.'
            ], 400);
        }

        if(!$customer->subscription || !$customer->subscription->activated) {
            return response()->json([
                'message' => self::THE_CUSTOMER_HASN_T_AN_ACTIVE_SUBSCRIPTION
            ]);
        }

        return response()->json($customer->subscription->pets);
    }

    /**
     * @param Request $request
     * @param $customerId
     * @param $petId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function removePet(Request $request, $customerId, $petId): JsonResponse
    {
        $input = [
            'pet_id' => $petId,
            'customer_id' => $customerId
        ];
        $request->request->add($input);

        $this->validate($request, [
            'pet_id' => 'required|exists:pets,id',
            'customer_id' => 'required|exists:customers,id'
        ]);

        $customer = Customer::find($customerId);

        $subscription = $customer->subscription;
        if(!$subscription || !$subscription->activated) {
            return response()->json([
                'message' => self::THE_CUSTOMER_HASN_T_AN_ACTIVE_SUBSCRIPTION
            ]);
        }

        $pet = Pet::find($petId);

        $status = 200;
        $message = 'Pet removed from subscription.';

        if(!$this->subscriptionService->removeFromSubscription($subscription, $pet)) {
            $message = 'Pet can\'t be removed from subscription.';
            $status = 400;
        }

        return response()->json([
            'message' => $message
        ], $status);
    }

    public function addPet(Request $request, $customerId)
    {
        $request->request->add(['customer_id' => $customerId]);

        $genders = join(',', Pet::getGenders());
        $lifestages = "/Puppy|Adult|Senior|^\d+ kg$/";

        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required',
            'gender' => 'required|in:' . $genders,
            'lifestage' => ['required', 'regex:' . $lifestages]
        ];

        $this->validate($request, $rules);

        $customer = Customer::find($customerId);

        $subscription = $customer->subscription;
        if(!$subscription) {
            $subscription = $this->subscriptionService->createSubscription($customer);
        }

        $pet = new Pet();
        $pet->fill($request->all([
            'name',
            'gender',
            'lifestage'
        ]));

        $status = 200;
        $message = 'Pet added to subscription.';

        if(!$this->subscriptionService->addPetToSubscription($subscription, $pet)) {
            $message = 'Pet can\'t be added to subscription.';
            $status = 400;
        }

        return response()->json([
            'message' => $message,
            'pet' => $pet
        ], $status);
    }

    /**
     * @param Request $request
     * @param $customerId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updateNextOrder(Request $request, $customerId): JsonResponse
    {
        $input = [
            'customer_id' => $customerId
        ];
        $request->request->add($input);

        $today = Carbon::now()->format('Y-m-d');

        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id',
            'next_order_date' => 'required|date|after_or_equal:' . $today
        ]);

        $customer = Customer::find($customerId);

        $subscription = $customer->subscription;

        if(!$subscription || !$subscription->activated) {
            return response()->json([
                'message' => self::THE_CUSTOMER_HASN_T_AN_ACTIVE_SUBSCRIPTION
            ]);
        }

        $nextOrderDate = Carbon::parse($request->get('next_order_date'));

        $status = 200;
        $message = 'Next order date was successfully updated.';

        $this->subscriptionService->updateNextOrder($subscription, $nextOrderDate);

        return response()->json([
            'message' => $message
        ], $status);
    }

    /**
     * @param Request $request
     * @param $customerId
     * @return JsonResponse|void
     * @throws ValidationException
     */
    public function orderNow(Request $request, $customerId)
    {
        $input = [
            'customer_id' => $customerId
        ];
        $request->request->add($input);

        $today = Carbon::now()->format('Y-m-d');

        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id',
            'estimated_shipping' => 'required|date|after_or_equal:' . $today
        ]);

        $customer = Customer::find($customerId);
        $subscription = $customer->subscription;

        if(!$subscription || !$subscription->activated) {
            return response()->json([
                'message' => self::THE_CUSTOMER_HASN_T_AN_ACTIVE_SUBSCRIPTION
            ]);
        }

        $estimatedShipping = Carbon::parse($request->get('estimated_shipping'));

        $order = $this->subscriptionService->dispatchNewOrder($estimatedShipping, $subscription);

        return response()->json([
            'message' => 'New order was successfully shipped',
            'order' => $order
        ]);
    }
}
