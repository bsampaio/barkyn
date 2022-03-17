<?php

namespace App\Services;

use App\Exceptions\CantUpdateSubscriptionPrices;
use App\Exceptions\SubscriptionMinimumAmountOfPetsNotReached;
use App\Models\Customer;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionsService
{
    //Get subscriptions by customer
    public function getSubscriptionsByCustomer(Customer $customer): ?Subscription
    {
        return $customer->subscription;
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
        //Remove pet
        $pet->delete();

        //Trigger calc of prices
        return self::updateSubscriptionPrices($subscription);
    }

    //Update subscription
    public function updateNextOrder(Subscription $subscription, Carbon $newDate, $dispatchNewOrder = true)
    {
        $subscription->next_order_date = $newDate;
        $subscription->update();

        if($dispatchNewOrder) {
            //Create new order. TODO: Can be made as a queued job.

        }
    }

    //Add a pet

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

        if(!self::updateSubscriptionPrices()) {
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
}
