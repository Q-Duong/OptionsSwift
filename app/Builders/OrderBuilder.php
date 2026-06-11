<?php

namespace App\Builders;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

final class OrderBuilder extends Builder
{
    public function  getAll()
    {
        $getAllOrder = Order::join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->select(
                'orders.id',
                'orders.created_at',
                'order_quantity',
                'order_price',
                'status_id',
                'unit_code',
                'unit_name',
                'ord_start_day',
                'ord_end_day',
                'ord_select',
                'ord_cty_name',
                'ord_type',
                'schedule_status'
            )
            ->orderBy('ord_start_day', 'DESC')
            ->paginate(20);
        return $getAllOrder;
    }

    public function getOneOrder($order_id)
    {
        $getOneOder = Order::join('accountants', 'accountants.order_id', '=', 'orders.id')
            ->join('units', 'units.id',  '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.id', $order_id)
            ->select(
                'accountants.order_id',
                'orders.order_detail_id',
                'customer_name',
                'customer_phone',
                'customer_address',
                'customer_note',
                'orders.unit_id',
                'ord_cty_name',
                'ord_start_day',
                'ord_time',
                'ord_select',
                'ord_doctor_read',
                'ord_film',
                'ord_form',
                'ord_print',
                'ord_form_print',
                'ord_print_result',
                'ord_film_sheet',
                'ord_note',
                'order_warning',
                'ord_deadline',
                'ord_deliver_results',
                'ord_email',
                'ord_list_file',
                'ord_list_file_path',
                'accountant_distance',
                'order_vat',
                'order_child',
                'order_surcharge',
                'order_all_in_one',
                'order_quantity',
                'order_cost',
                'order_percent_discount',
                'order_price',
                'status_id'
            )
            ->first();
        return $getOneOder;
    }
    public function getScheduleTechnologist($firstDayofThisMonth, $lastDayofThisMonth)
    {
        $getScheduleTechnologist = Order::join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('car_ktvs', 'car_ktvs.order_id', '=', 'orders.id')
            ->whereBetween('order_details.ord_start_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->whereBetween('order_details.ord_end_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->where('car_ktvs.car_active', 1)
            ->select(
                'car_name',
                'car_active',
                'status_id',
                'order_surcharge',
                'car_ktv_name_1',
                'car_ktv_name_2',
                'ord_start_day',
                'ord_end_day',
                'order_child',
                'order_updated',
                'car_ktvs.order_id',
                'unit_name',
                'car_ktvs.id',
                'ord_select',
                'ord_cty_name',
                'customer_address',
                'customer_note',
                'ord_note',
                'ord_list_file',
                'ord_list_file_path',
                'customer_name',
                'customer_phone',
                'ord_time',
                'order_quantity',
                'order_quantity_draft',
                'order_note_ktv'
            )
            ->orderBy('order_details.ord_start_day', 'ASC')
            ->orderBy('orders.order_child', 'DESC')
            ->get();
        return $getScheduleTechnologist;
    }

    public function getScheduleDetails($firstDayofThisMonth, $lastDayofThisMonth)
    {
        $getScheduleDetails = Order::join('accountants', 'accountants.order_id', '=', 'orders.id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('car_ktvs', 'car_ktvs.order_id', '=', 'orders.id')
            ->whereBetween('order_details.ord_start_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->whereBetween('order_details.ord_end_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->where('car_ktvs.car_active', 1)
            ->select(
                'status_id',
                'car_ktvs.order_id',
                'car_ktvs.id',
                'car_ktv_name_1',
                'car_ktv_name_2',
                'car_active',
                'car_name',
                'unit_code',
                'unit_name',
                'customer_address',
                'customer_note',
                'customer_name',
                'customer_phone',
                'orders.order_detail_id',
                'ord_select',
                'ord_cty_name',
                'ord_time',
                'ord_list_file',
                'ord_list_file_path',
                'ord_total_file_name',
                'ord_total_file_path',
                'ord_delivery_date',
                'ord_start_day',
                'ord_end_day',
                'ord_doctor_read',
                'ord_film',
                'ord_form',
                'ord_print',
                'ord_form_print',
                'ord_print_result',
                'ord_film_sheet',
                'ord_note',
                'ord_deadline',
                'ord_deliver_results',
                'ord_email',
                'accountant_doctor_read',
                'accountant_35X43',
                'accountant_polime',
                'accountant_8X10',
                'accountant_10X12',
                'accountant_film_bag',
                'accountant_note',
                'order_surcharge',
                'order_child',
                'order_quantity',
                'order_quantity_draft',
                'order_note_ktv',
                'order_warning',
                'order_updated',
                'order_send_result'
            )
            ->orderBy('order_details.ord_start_day', 'ASC')
            ->orderBy('orders.order_child', 'DESC')
            ->get();
        return $getScheduleDetails;
    }

    public function getScheduleDetailsForSearch($firstDayofThisMonth, $lastDayofThisMonth, $target)
    {
        $getScheduleDetails = Order::join('accountants', 'accountants.order_id', '=', 'orders.id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('car_ktvs', 'car_ktvs.order_id', '=', 'orders.id')
            ->whereBetween('order_details.ord_start_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->whereBetween('order_details.ord_end_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->where('car_ktvs.car_active', 1)
            ->where('ord_cty_name', $target)
            ->select(
                'status_id',
                'car_ktvs.order_id',
                'car_ktvs.id',
                'car_ktv_name_1',
                'car_ktv_name_2',
                'car_active',
                'car_name',
                'unit_code',
                'unit_name',
                'customer_address',
                'customer_note',
                'customer_name',
                'customer_phone',
                'orders.order_detail_id',
                'ord_select',
                'ord_cty_name',
                'ord_time',
                'ord_list_file',
                'ord_list_file_path',
                'ord_total_file_name',
                'ord_total_file_path',
                'ord_delivery_date',
                'ord_start_day',
                'ord_end_day',
                'ord_doctor_read',
                'ord_film',
                'ord_form',
                'ord_print',
                'ord_form_print',
                'ord_print_result',
                'ord_film_sheet',
                'ord_note',
                'ord_deadline',
                'ord_deliver_results',
                'ord_email',
                'accountant_doctor_read',
                'accountant_35X43',
                'accountant_polime',
                'accountant_8X10',
                'accountant_10X12',
                'accountant_film_bag',
                'accountant_note',
                'order_surcharge',
                'order_child',
                'order_quantity',
                'order_quantity_draft',
                'order_note_ktv',
                'order_warning',
                'order_updated',
                'order_send_result'
            )
            ->orderBy('order_details.ord_start_day', 'ASC')
            ->orderBy('orders.order_child', 'DESC')
            ->get();
        return $getScheduleDetails;
    }
}
