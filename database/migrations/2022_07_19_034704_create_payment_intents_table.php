<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_intents', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->string('ipag_id')->nullable();

            $table->integer('amount')->nullable()->unsigned();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('callback_url')->nullable();

            $table->string('payment_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_installments')->nullable();
            $table->string('payment_capture')->nullable();
            $table->string('payment_fraud_analysis')->nullable();
            $table->string('payment_softdescriptor')->nullable();
            $table->string('payment_pix_expires_in')->nullable();

            $table->string('card_holder')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_expiry_month')->nullable();
            $table->string('card_expiry_year')->nullable();
            $table->string('card_cvv')->nullable();
            $table->string('card_token')->nullable();
            $table->string('card_tokenize')->nullable();

            $table->string('boleto_due_date')->nullable();
            $table->string('boleto_instructions')->nullable();

            $table->string('customer_name')->nullable();
            $table->string('customer_cpf_cnpj')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_birthdate')->nullable();
            $table->string('customer_ip')->nullable();

            $table->string('metadata')->nullable();

            $table->string('status')->default('requires_action');
            $table->string('pix_url')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_intents');
    }
};
