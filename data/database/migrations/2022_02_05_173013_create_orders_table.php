<?php

use App\Models\Address;
use App\Structure\Enum\DeliveryType;
use App\Structure\Enum\OrderStatus;
use App\Util\EnumUtil;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->char('order_id_salt', '4');
            $table->char('buyer_name');
            $table->char('buyer_email')->comment('email');
            $table->enum('delivery_type', EnumUtil::getEnumValues(DeliveryType::class));
            $table->enum('status', EnumUtil::getEnumValues(OrderStatus::class));
            $table->foreignIdFor(Address::class, 'billing_address_id')->constrained('addresses')->onUpdate('cascade');
            $table->foreignIdFor(Address::class, 'shipping_address_id')->constrained('addresses')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
