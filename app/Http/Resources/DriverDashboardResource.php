<?php

namespace App\Http\Resources;

use App\Models\RideRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Sos;
use Illuminate\Database\Eloquent\Builder;
class DriverDashboardResource extends JsonResource
{
    public function toArray($request)
    {
        $on_ride_request = $this->driverRideRequestDetail()->whereNotIn('status', ['canceled'])->where('is_driver_rated',false)
            // ->whereHas('payment',function ($q) {
            //     $q->whereNull('payment_status')->orWhere('payment_status', 'pending');
            // })
            ->first();

        $pending_payment_ride_request = $this->driverRideRequestDetail()->where('status', 'completed')->where('is_driver_rated',true)
            ->whereHas('payment',function ($q) {
                $q->where('payment_status', 'pending');
            })
            ->first();
        $rider = isset($on_ride_request) && optional($on_ride_request->rider) ? $on_ride_request->rider :  null;
        $payment = isset($pending_payment_ride_request) && optional($pending_payment_ride_request->payment) ? $pending_payment_ride_request->payment : null;

        // my edited code
        $ride_request = RideRequest::where('status', 'new_ride_requested')
        ->where('service_id', $this->service_id)
        ->first();
        $userId = auth()->id();
        $cancelled_driver_ids = $ride_request->cancelled_driver_ids ?? [];
        // if the array has the id or not
        $array_has_id = in_array($userId, $cancelled_driver_ids);
        $notification_data = null;
        if ($ride_request and !$array_has_id) {
            $notification_data = [
                'id' => $ride_request->id,
                'type' => 'new_ride_requested',
                'subject' => __('message.new_ride_requested'),
                'message' => __('message.ride.new_ride_requested'),
            ];
            $notification_data['success'] = true;
            $notification_data['success_type'] = $ride_request->status;
            $notification_data['success_message'] = __('message.ride.new_ride_requested');
            $notification_data['result'] = new RideRequestResource($ride_request);
            $notification_data['clickable'] = "1";
        }
        // end of my edited code

        return [
            'id'                => $this->id,
            'display_name'      => $this->display_name,
            'email'             => $this->email,
            'username'          => $this->username,
            'user_type'         => $this->user_type,
            'profile_image'     => getSingleMedia($this, 'profile_image',null),
            'status'            => $this->status,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'on_ride_request'   => isset($on_ride_request) ? new RideRequestResource($on_ride_request) : null,
            'rider'             => isset($rider) ? new UserResource($rider) : null,
            'payment'           => isset($payment) ? new PaymentResource($payment) : null,
            'payload'           => $notification_data == null ? null : json_encode($notification_data), // my edited code
        ];
    }
}
