<?php

namespace App\Services;

use App\Exceptions\CantUpdateSubscriptionPrices;
use App\Exceptions\SubscriptionMinimumAmountOfPetsNotReached;
use App\Exceptions\UserAlreadySubscripted;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Pet;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionsService
{

    /**
     * Get subscriptions by customer
     * @param Customer $customer
     * @return Subscription|null
     */
    public function getSubscriptionsByCustomer(Customer $customer): ?Subscription
    {
        return $customer->subscription;
    }

    /**
     * @param Customer $customer
     * @return Subscription
     * @throws UserAlreadySubscripted
     */
    public function createSubscription(Customer $customer): Subscription
    {
        if($customer->subscription) {
            throw new UserAlreadySubscripted();
        }

        $subscription = new Subscription();
        $subscription->next_order_date = Carbon::now()->addMonth();
        $subscription->customer()->associate($customer);
        $subscription->save();

        return $subscription;
    }

    //List all pets in subscription
    public function getPetsSubscribed(Subscription $subscription): ?array
    {
        return $subscription->pets;
    }

    /**
     * Remove pet from subscription
     */
    public function removeFromSubscription(Subscription $subscription, Pet $pet): bool
    {
        $pet->delete();

        if($subscription->pets()->count() < 1) {
            $subscription->activated = false;
            $subscription->update();
        }

        return self::updateSubscriptionPrices($subscription);
    }

    /**
     * Update next order.
     *
     * @param Subscription $subscription
     * @param Carbon $newDate
     * @param bool $dispatchNewOrder
     * @return void
     */
    public function updateNextOrder(Subscription $subscription, Carbon $newDate, bool $dispatchNewOrder = true)
    {
        $subscription->next_order_date = $newDate;
        $subscription->update();

        if($dispatchNewOrder) {
            $this->dispatchNewOrder($newDate, $subscription);
        }
    }

    /**
     * Adds a pet to a given subscription.
     * @param Subscription $subscription
     * @param Pet $pet
     * @return bool
     * @throws SubscriptionMinimumAmountOfPetsNotReached|CantUpdateSubscriptionPrices
     */
    public function addPetToSubscription(Subscription $subscription, Pet $pet): bool
    {
        $pet->subscription()->associate($subscription);
        $pet->save();

        return self::activateSubscription($subscription);
    }

    //Dispatch new food order


    //Calc prices
    /**
     * @throws SubscriptionMinimumAmountOfPetsNotReached|CantUpdateSubscriptionPrices
     */
    public static function activateSubscription(Subscription $subscription): bool
    {
        $petsCount = $subscription->pets->count();

        if(!$petsCount) {
            throw new SubscriptionMinimumAmountOfPetsNotReached();
        }

        if(!self::updateSubscriptionPrices($subscription)) {
            throw new CantUpdateSubscriptionPrices();
        }

        return $subscription->update([
            'activated' => true
        ]);
    }

    public static function getBasePrice($amount = 1)
    {
        return Subscription::PRICE__UNIT * $amount;
    }

    public static function getTotalPriceWithDiscount($amount)
    {
        $basePrice = self::getBasePrice($amount);

        return $basePrice * (1 - $amount/100);
    }

    public static function updateSubscriptionPrices(Subscription $subscription): bool
    {
        $petsCount = $subscription->pets->count();

        return $subscription->update([
            'base_price' => self::getBasePrice($petsCount),
            'total_price' => self::getTotalPriceWithDiscount($petsCount)
        ]);
    }

    /**
     * @param Carbon $newDate
     * @param Subscription $subscription
     * @return void
     */
    public function dispatchNewOrder(Carbon $newDate, Subscription $subscription): ?Order
    {
        $order = new Order();

        $order->fill([
            'estimated_shipping' => $newDate,
            'subscription_id' => $subscription->id
        ]);

        $order->save();

        return $order;
    }
}
